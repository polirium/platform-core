<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/settings::permission.settings',
        'flag' => 'settings',
    ],
    [
        'name' => 'core/settings::permission.settings.index',
        'flag' => 'settings.index',
        'parent_flag' => 'settings',
    ],
    [
        'name' => 'core/settings::permission.settings.general',
        'flag' => 'settings.general',
        'parent_flag' => 'settings',
    ],
    [
        'name' => 'core/settings::permission.settings.email',
        'flag' => 'settings.email',
        'parent_flag' => 'settings',
    ],
    [
        'name' => 'core/settings::permission.settings.media',
        'flag' => 'settings.media',
        'parent_flag' => 'settings',
    ],
    [
        'name' => 'core/settings::permission.settings.system',
        'flag' => 'settings.system',
        'parent_flag' => 'settings',
    ],
];
