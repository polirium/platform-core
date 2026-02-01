<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Menu
    |--------------------------------------------------------------------------
    |
    | This option controls the dashboard menu.
    | Add 'permission' key to control visibility based on user permissions.
    | If 'permission' is not set, menu item is visible to all authenticated users.
    |
     */
    [
        'id' => 'core',
        'name' => 'core/base::general.home',
        'route' => 'core.index',
        'icon' => 'home',
        'sort' => 0,
        // No permission = visible to all
    ],
    /**
     * User Manager Menu
     */
    [
        'id' => 'core.user',
        'name' => 'core/base::general.staff',
        'route' => null,
        'icon' => 'eye',
        'sort' => 7,
        'permission' => 'users.view',
    ],
    [
        'id' => 'core.user.manager',
        'name' => 'core/base::general.staff_management',
        'parent' => 'core.user',
        'route' => 'core.users.index',
        'icon' => 'map-pins',
        'sort' => 1,
        'permission' => 'users.view',
    ],


    /**
     * Setting Menu
     */
    [
        'id' => 'core.setting',
        'name' => 'core/base::general.settings',
        'route' => null,
        'icon' => 'settings',
        'sort' => 8,
        // Parent menus are auto-hidden if no children are visible
    ],
    [
        'id' => 'core.setting-general',
        'name' => 'core/base::general.general_settings',
        'parent' => 'core.setting',
        'route' => 'core.settings.index',
        'icon' => 'settings-2',
        'sort' => 0,
        'permission' => 'settings.view',
    ],
    [
        'id' => 'core.setting-brand',
        'name' => 'core/base::general.brands',
        'parent' => 'core.setting',
        'route' => 'core.brands.index',
        'icon' => 'star',
        'sort' => 1,
        'permission' => 'brands.view',
    ],
    [
        'id' => 'core.setting-branch',
        'name' => 'core/base::general.branches',
        'parent' => 'core.setting',
        'route' => 'core.branches.index',
        'icon' => 'map-pins',
        'sort' => 2,
        'permission' => 'branches.view',
    ],
    [
        'id' => 'core.setting-role',
        'name' => 'core/base::general.role_management_menu',
        'parent' => 'core.setting',
        'route' => 'core.roles.index',
        'icon' => 'user-check',
        'sort' => 3,
        'permission' => 'roles.view',
    ],
    [
        'id' => 'core.setting-modules',
        'name' => 'core/base::general.module_management',
        'parent' => 'core.setting',
        'route' => 'core.modules.index',
        'icon' => 'package',
        'sort' => 4,
        'permission' => 'modules.view',
    ],
    [
        'id' => 'core.activity-logs',
        'name' => 'core/base::general.activity_logs',
        'parent' => 'core.setting',
        'route' => 'core.activity-logs.index',
        'icon' => 'activity',
        'sort' => 5,
        'permission' => 'activity-logs.view',
    ],
];
