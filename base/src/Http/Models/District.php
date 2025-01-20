<?php

namespace Polirium\Core\Base\Http\Models;

use Kjmtrue\VietnamZone\Models\District as ModelsDistrict;
use Polirium\Core\Base\Http\Models\BaseModel;

class District extends ModelsDistrict
{
    protected $table = "districts";

    protected $fillable = [
        'name',
        'gso_id',
        'province_id',
    ];
}
