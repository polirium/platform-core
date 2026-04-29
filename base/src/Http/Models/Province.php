<?php

namespace Polirium\Core\Base\Http\Models;

use Kjmtrue\VietnamZone\Models\Province as ModelsProvince;

class Province extends ModelsProvince
{
    protected $table = 'provinces';

    protected $fillable = [
        'name',
        'gso_id',
    ];
}
