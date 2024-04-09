<?php

namespace Polirium\Core\Base\Http\Models\Branch;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Polirium\Core\Base\Http\Models\BaseModel;
use Polirium\Core\Base\Http\Models\Brand\Brand;
use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Base\Http\Models\Ward;

// Chi nhánh
class Branch extends BaseModel
{
    protected $table = "branches";

    protected $fillable = [
        "uuid",
        "name",
        "phone",
        "phone_2",
        "email",
        "address",
        "province_id",
        "district_id",
        "ward_id",
        "status",
        "user_id",
    ];

    public function getStatusNameAttribute()
    {
        return match ((int)$this->status) {
            1 => trans("Đang hoạt động"),
            default => trans("Không hoạt động"),
        };
    }

    /**
     * Chi nhánh có nhiều địa chỉ lấy hàng
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function takingAddresses(): HasMany
    {
        return $this->hasMany(BranchTakingAddress::class, 'branch_id');
    }

    /**
     * The users that belong to the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_branches', 'branch_id', 'user_id')
        ->withTimestamps()
        ->withPivot(["id", "active"]);
    }

    /**
     * Get the province that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault(["name" => null]);
    }

    /**
     * Get the district that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault(["name" => null]);
    }

    /**
     * Get the ward that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id')->withDefault(["name" => null]);
    }

        /**
     * The brands that belong to the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brands_branches', 'branch_id', 'brand_id')
        ->withPivot(["id"])
        ->withTimestamps();
    }
}
