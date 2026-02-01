<?php

return [
    [
        'id' => 'media',
        'name' => trans('Media'),
        'icon' => 'files',
        'route' => null,
        'parent' => 'root',
        'sort' => 50,
    ],
    [
        'id' => 'media.manager',
        'name' => trans('Quản lý Media'),
        'icon' => 'folders',
        'route' => 'media.index',
        'parent' => 'media',
        'sort' => 1,
        'permission' => 'media.index',
    ],
];
