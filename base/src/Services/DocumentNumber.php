<?php

namespace Polirium\Core\Base\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Sequential document codes (BH/00001, PT/00001…) that stay unique when
 * several users save at the same time.
 *
 * The counter row is locked until the caller's transaction commits, so call
 * this inside the transaction that saves the document: a rolled-back save
 * gives its number back.
 */
class DocumentNumber
{
    /**
     * @param  string  $prefix  code prefix, e.g. "BH"
     * @param  string  $table  table holding existing codes, used once to start the counter
     * @param  string  $column  code column in that table
     */
    public static function next(string $prefix, string $table, string $column = 'code', int $pad = 5): string
    {
        return DB::transaction(function () use ($prefix, $table, $column, $pad) {
            $row = self::lockedRow($prefix, $table, $column);
            $number = (int) $row->last_number + 1;

            DB::table('document_sequences')
                ->where('id', $row->id)
                ->update(['last_number' => $number, 'updated_at' => now()]);

            return $prefix . '/' . str_pad((string) $number, $pad, '0', STR_PAD_LEFT);
        });
    }

    /**
     * The highest number already used with this prefix, so a new counter
     * continues after codes created before counters existed.
     */
    public static function highestUsed(string $prefix, string $table, string $column = 'code'): int
    {
        $start = mb_strlen($prefix) + 2; // SUBSTRING is 1-based and skips "PREFIX/"

        return (int) DB::table($table)
            ->where($column, 'like', addcslashes($prefix, '%_\\') . '/%')
            ->selectRaw("MAX(CAST(SUBSTRING({$column}, {$start}) AS UNSIGNED)) as max_number")
            ->value('max_number');
    }

    private static function lockedRow(string $prefix, string $table, string $column): object
    {
        $row = DB::table('document_sequences')->where('prefix', $prefix)->lockForUpdate()->first();
        if ($row) {
            return $row;
        }

        try {
            DB::table('document_sequences')->insert([
                'prefix' => $prefix,
                'last_number' => self::highestUsed($prefix, $table, $column),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Another request created the counter first: use theirs.
        }

        return DB::table('document_sequences')->where('prefix', $prefix)->lockForUpdate()->first();
    }
}
