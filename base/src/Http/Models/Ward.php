<?php

namespace Polirium\Core\Base\Http\Models;

use Polirium\VietnamAdministrativeUnits\Models\Ward as BaseWard;

class Ward extends BaseWard
{
    protected $table = 'wards';

    protected $fillable = [
        'name',
        'gso_id',
        'type',
        'province_id',
        'district_id',
    ];
}
