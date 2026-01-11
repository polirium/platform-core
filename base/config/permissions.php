<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.dashboard',
        'flag' => 'dashboard',
    ],
    [
        'name' => 'core/base::permission.dashboard.index',
        'flag' => 'dashboard.index',
        'parent_flag' => 'dashboard',
    ],

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.users',
        'flag' => 'users',
    ],
    [
        'name' => 'core/base::permission.users.index',
        'flag' => 'users.index',
        'parent_flag' => 'users',
    ],
    [
        'name' => 'core/base::permission.users.create',
        'flag' => 'users.create',
        'parent_flag' => 'users',
    ],
    [
        'name' => 'core/base::permission.users.edit',
        'flag' => 'users.edit',
        'parent_flag' => 'users',
    ],
    [
        'name' => 'core/base::permission.users.delete',
        'flag' => 'users.delete',
        'parent_flag' => 'users',
    ],
    [
        'name' => 'core/base::permission.users.impersonate',
        'flag' => 'users.impersonate',
        'parent_flag' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.roles',
        'flag' => 'roles',
    ],
    [
        'name' => 'core/base::permission.roles.index',
        'flag' => 'roles.index',
        'parent_flag' => 'roles',
    ],
    [
        'name' => 'core/base::permission.roles.create',
        'flag' => 'roles.create',
        'parent_flag' => 'roles',
    ],
    [
        'name' => 'core/base::permission.roles.edit',
        'flag' => 'roles.edit',
        'parent_flag' => 'roles',
    ],
    [
        'name' => 'core/base::permission.roles.delete',
        'flag' => 'roles.delete',
        'parent_flag' => 'roles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Branches Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.branches',
        'flag' => 'branches',
    ],
    [
        'name' => 'core/base::permission.branches.index',
        'flag' => 'branches.index',
        'parent_flag' => 'branches',
    ],
    [
        'name' => 'core/base::permission.branches.create',
        'flag' => 'branches.create',
        'parent_flag' => 'branches',
    ],
    [
        'name' => 'core/base::permission.branches.edit',
        'flag' => 'branches.edit',
        'parent_flag' => 'branches',
    ],
    [
        'name' => 'core/base::permission.branches.delete',
        'flag' => 'branches.delete',
        'parent_flag' => 'branches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Brands Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.brands',
        'flag' => 'brands',
    ],
    [
        'name' => 'core/base::permission.brands.index',
        'flag' => 'brands.index',
        'parent_flag' => 'brands',
    ],
    [
        'name' => 'core/base::permission.brands.create',
        'flag' => 'brands.create',
        'parent_flag' => 'brands',
    ],
    [
        'name' => 'core/base::permission.brands.edit',
        'flag' => 'brands.edit',
        'parent_flag' => 'brands',
    ],
    [
        'name' => 'core/base::permission.brands.delete',
        'flag' => 'brands.delete',
        'parent_flag' => 'brands',
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.modules',
        'flag' => 'modules',
    ],
    [
        'name' => 'core/base::permission.modules.index',
        'flag' => 'modules.index',
        'parent_flag' => 'modules',
    ],
    [
        'name' => 'core/base::permission.modules.manage',
        'flag' => 'modules.manage',
        'parent_flag' => 'modules',
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.settings',
        'flag' => 'settings',
    ],
    [
        'name' => 'core/base::permission.settings.index',
        'flag' => 'settings.index',
        'parent_flag' => 'settings',
    ],
    [
        'name' => 'core/base::permission.settings.edit',
        'flag' => 'settings.edit',
        'parent_flag' => 'settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/base::permission.widgets',
        'flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.index',
        'flag' => 'widgets.index',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.manage',
        'flag' => 'widgets.manage',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.stats',
        'flag' => 'widgets.stats',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.welcome',
        'flag' => 'widgets.welcome',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.quick_access',
        'flag' => 'widgets.quick_access',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.revenue',
        'flag' => 'widgets.revenue',
        'parent_flag' => 'widgets',
    ],
    [
        'name' => 'core/base::permission.widgets.sales',
        'flag' => 'widgets.sales',
        'parent_flag' => 'widgets',
    ],
];
