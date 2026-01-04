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
        'route' => 'core.index',
        'icon' => 'home',
        'sort' => 0,
    ],
    /**
     * User Manager Menu
     */
    [
        'id' => 'core.user',
        'name' => trans('Nhân viên'),
        'route' => null,
        'icon' => 'eye',
        'sort' => 0,
    ],
    [
        'id' => 'core.user.manager',
        'name' => trans('Quản lý nhân viên'),
        'parent' => 'core.user',
        'route' => 'core.users.index',
        'icon' => 'map-pins',
        'sort' => 1,
    ],


    /**
     * Setting Menu
     */
    [
        'id' => 'core.setting',
        'name' => trans('Cài đặt'),
        'route' => null,
        'icon' => 'settings',
        'sort' => 0,
    ],
    [
        'id' => 'core.setting-general',
        'name' => trans('Cài đặt chung'),
        'parent' => 'core.setting',
        'route' => 'core.settings.index',
        'icon' => 'settings-2',
        'sort' => 0,
    ],
    [
        'id' => 'core.setting-brand',
        'name' => trans('Thương hiệu'),
        'parent' => 'core.setting',
        'route' => 'core.brands.index',
        'icon' => 'star',
        'sort' => 1,
    ],
    [
        'id' => 'core.setting-branch',
        'name' => trans('Chi nhánh'),
        'parent' => 'core.setting',
        'route' => 'core.branches.index',
        'icon' => 'map-pins',
        'sort' => 2,
    ],
    [
        'id' => 'core.setting-role',
        'name' => trans('Quản lý vai trò'),
        'parent' => 'core.setting',
        'route' => 'core.roles.index',
        'icon' => 'user-check',
        'sort' => 3,
    ],
    [
        'id' => 'core.setting-modules',
        'name' => trans('Quản lý Module'),
        'parent' => 'core.setting',
        'route' => 'core.modules.index',
        'icon' => 'package',
        'sort' => 4,
    ],
    [
        'id' => 'core.activity-logs',
        'name' => trans('Lịch sử hoạt động'),
        'parent' => 'core.setting',
        'route' => 'core.activity-logs.index',
        'icon' => 'activity',
        'sort' => 5,
    ],
];
