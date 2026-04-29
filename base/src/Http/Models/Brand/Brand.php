<?php

namespace Polirium\Core\Base\Http\Models\Brand;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Polirium\Core\Base\Http\Models\BaseModel;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\User;

// Thương hiệu
class Brand extends BaseModel
{
    protected $table = 'brands';

    protected $fillable = [
        'uuid',
        'name',
        'status',
        'note',
        'user_id',
    ];

    // public function getStatusNameAttribute()
    // {
    //     return match ((int)$this->status) {
    //         1 => trans("Đang hoạt động"),
    //         default => trans("Không hoạt động"),
    //     };
    // }

    /**
     * The branches that belong to the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'brands_branches', 'brand_id', 'branch_id')
        ->withPivot(['id'])
        ->withTimestamps();
    }

    /**
     * Get the user that owns the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => null]);
    }
}
