<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Menu
    |--------------------------------------------------------------------------
    |
    | This option controls the dashboard menu.
    |
     */
    [
        'id' => 'core',
        'name' => trans('Trang chủ'),
        'route' => null,
        'icon' => 'building',
        'sort' => 0,
    ],
    [
        'id' => 'core.dashboard',
        'name' => trans('Trang chủ 2 2'),
        'route' => 'core.index',
        'parent' => 'core',
        'icon' => 'eye',
        'sort' => 0,
    ],

    [
        'id' => 'core.user',
        'name' => trans('Quản lý người dùng'),
        'route' => null,
        'icon' => 'eye',
        'sort' => 0,
    ],
];
