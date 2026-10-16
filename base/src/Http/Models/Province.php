<?php

namespace Polirium\Core\Base\Http\Models;

use Polirium\VietnamAdministrativeUnits\Models\Province as BaseProvince;

class Province extends BaseProvince
{
    protected $table = 'provinces';

    protected $fillable = [
        'name',
        'gso_id',
    ];
}
