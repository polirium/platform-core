<?php

namespace Polirium\Core\Base\Services;

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
            $number = self::take($prefix, $table, $column);

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

    /** Increments the counter under an exclusive row lock and returns the new number. */
    private static function take(string $prefix, string $table, string $column): int
    {
        $counters = DB::table('document_sequences');

        // Plain read first: a locking read of a missing row takes a gap lock,
        // which blocks another save's insert of the same counter.
        $row = (clone $counters)->where('prefix', $prefix)->exists()
            ? (clone $counters)->where('prefix', $prefix)->lockForUpdate()->first()
            : null;

        if ($row) {
            $number = (int) $row->last_number + 1;
            (clone $counters)->where('id', $row->id)->update(['last_number' => $number, 'updated_at' => now()]);

            return $number;
        }

        // First use of the prefix. Locking the missing row and then inserting
        // deadlocks when two saves start the same counter (shared locks that both
        // want to upgrade). An upsert takes the exclusive lock directly: the first
        // save creates the counter, the others wait and then increment it.
        (clone $counters)->upsert(
            [[
                'prefix' => $prefix,
                'last_number' => self::highestUsed($prefix, $table, $column) + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]],
            ['prefix'],
            ['last_number' => DB::raw('last_number + 1'), 'updated_at' => now()]
        );

        return (int) (clone $counters)->where('prefix', $prefix)->lockForUpdate()->value('last_number');
    }
}
