<?php

namespace Polirium\Core\Base\Http\Models;

use Polirium\VietnamAdministrativeUnits\Models\District as BaseDistrict;

class District extends BaseDistrict
{
    protected $table = 'districts';

    protected $fillable = [
        'name',
        'gso_id',
        'province_id',
    ];
}
