<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Management
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'core/media::permission.media',
        'flag' => 'media',
    ],
    [
        'name' => 'core/media::permission.media.index',
        'flag' => 'media.index',
        'parent_flag' => 'media',
    ],
    [
        'name' => 'core/media::permission.media.upload',
        'flag' => 'media.upload',
        'parent_flag' => 'media',
    ],
    [
        'name' => 'core/media::permission.media.update',
        'flag' => 'media.update',
        'parent_flag' => 'media',
    ],
    [
        'name' => 'core/media::permission.media.delete',
        'flag' => 'media.delete',
        'parent_flag' => 'media',
    ],
    [
        'name' => 'core/media::permission.media.download',
        'flag' => 'media.download',
        'parent_flag' => 'media',
    ],
];
