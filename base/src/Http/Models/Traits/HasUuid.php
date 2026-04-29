<?php

namespace Polirium\Core\Base\Http\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid()
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::orderedUuid();
            }
        });
    }

    public function scopeFindByUuid(Builder $query, string $uuid): self
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
