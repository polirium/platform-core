<?php

namespace Polirium\Core\Base\Http\Models;

use Kjmtrue\VietnamZone\Models\Ward as ModelsWard;

class Ward extends ModelsWard
{
    protected $table = 'wards';

    protected $fillable = [
        'name',
        'gso_id',
        'district_id',
    ];
}
