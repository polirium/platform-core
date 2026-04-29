<?php

namespace Polirium\Core\Base\Http\Models\Branch;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Polirium\Core\Base\Http\Models\BaseModel;
use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\Ward;

class BranchTakingAddress extends BaseModel
{
    protected $table = 'branch_taking_addresses';

    protected $fillable = [
        'uuid',
        'branch_id',
        'address',
        'phone',
        'province_id',
        'district_id',
        'ward_id',
        'user_id',
    ];

    /**
     * Get the province that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault(['name' => null]);
    }

    /**
     * Get the district that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault(['name' => null]);
    }

    /**
     * Get the ward that owns the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id')->withDefault(['name' => null]);
    }
}
