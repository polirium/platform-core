<?php

namespace Polirium\Core\Base\Http\Models;

class Country extends BaseModel
{
    protected $table = 'countries';

    protected $fillable = [
        'name',
        'nationality',
        'code',
        'order',
    ];
}
