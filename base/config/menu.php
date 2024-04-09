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

    [
        'id' => 'core.setting',
        'name' => trans('Cài đặt'),
        'route' => null,
        'icon' => 'settings',
        'sort' => 0,
    ],
    [
        'id' => 'core.setting-brand',
        'name' => trans('Thương hiệu'),
        'parent' => 'core.setting',
        'route' => 'core.brands.index',
        'icon' => 'star',
        'sort' => 0,
    ],
    [
        'id' => 'core.setting-branch',
        'name' => trans('Chi nhánh'),
        'parent' => 'core.setting',
        'route' => 'core.branches.index',
        'icon' => 'map-pins',
        'sort' => 1,
    ],
];
