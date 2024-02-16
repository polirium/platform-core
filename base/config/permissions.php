<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Authentication Guard
    |--------------------------------------------------------------------------
    |
    */
    [
        'name' => trans('core/base::permission.core'),
        'flag' => 'core',
    ],
    [
        'name' => trans('core/base::permission.core.index'),
        'flag' => 'core.index',
        'parent_flag' => 'core',
    ],
];
