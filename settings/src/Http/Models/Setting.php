<?php

namespace Polirium\Core\Settings\Http\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Base\Http\Models\BaseModel;
use Illuminate\Support\Collection;
use Polirium\Core\Settings\Contracts\Setting as SettingContract;
use Polirium\Core\Settings\Facades\Settings;

class Setting extends BaseModel implements SettingContract
{
    public $timestamps = false;

    protected $table = 'settings';

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function getValue(string $key, $default = null, $teamId = null)
    {
        $value = static::query()
            ->where('key', $key)
            ->when(
                $teamId !== false,
                fn (Builder $query) => $query->where(
                    static::make()->getTable() . '.team_id',
                    $teamId,
                ),
            )
            ->value('value');

        return $value ?? $default;
    }

    public static function getAll($teamId = null, $keys = null): array|Arrayable
    {
        return static::baseBulkQuery($teamId, $keys)->get();
    }

    public static function has($key, $teamId = null): bool
    {
        return static::query()
            ->where('key', $key)
            ->when(
                $teamId !== false,
                fn (Builder $query) => $query->where(
                    static::make()->getTable() . '.team_id',
                    $teamId,
                ),
            )
            ->exists();
    }

    public static function removeSetting($key, $teamId = null): void
    {
        static::query()
            ->where('key', $key)
            ->when(
                $teamId !== false,
                fn (Builder $query) => $query->where(
                    static::make()->getTable() . '.team_id',
                    $teamId,
                ),
            )
            ->delete();
    }

    public static function set(string $key, $value = null, $teamId = null)
    {
        $data = ['key' => $key];

        if ($teamId !== false) {
            $data['team_id'] = $teamId;
        }

        return static::updateOrCreate($data, compact('value'));
    }

    public static function flush($teamId = null, $keys = null): void
    {
        static::baseBulkQuery($teamId, $keys)->delete();
    }

    protected static function baseBulkQuery($teamId, $keys): Builder
    {
        $keys = static::normalizeKeys($keys);

        return static::query()
            ->when(
                // False means we want settings without a context set.
                $keys === false,
                fn (Builder $query) => $query->where('key', 'NOT LIKE', '%' . Settings::getKeyGenerator()->contextPrefix() . '%'),
            )
            ->when(
                // When keys is a string, we're trying to do a partial lookup for context
                is_string($keys),
                fn (Builder $query) => $query->where('key', 'LIKE', "%{$keys}"),
            )
            ->when(
                $keys instanceof Collection && $keys->isNotEmpty(),
                fn (Builder $query) => $query->whereIn('key', $keys),
            )
            ->when(
                $teamId !== false,
                fn (Builder $query) => $query->where(
                    static::make()->getTable() . '.team_id',
                    $teamId,
                ),
            );
    }

    protected static function normalizeKeys($keys): string|Collection|bool
    {
        if (is_bool($keys)) {
            return $keys;
        }

        if (is_string($keys)) {
            return $keys;
        }

        return collect($keys)->flatten()->filter();
    }
}
