<?php

$basePathCss = 'core/ui/css/';
$basePathJs = 'core/ui/js/';
$libsPath = 'core/ui/libs/';

return [
    'css' => [
        'core' => $basePathCss.'polirium-core.min.css',
        'flags' => $basePathCss.'polirium-flags.min.css',
        'vendors' => $basePathCss.'polirium-vendors.min.css',
        'app' => $basePathCss.'app.min.css',
    ],
    'js' => [
        'polirium' => $basePathJs.'polirium.min.js',
        'theme' => $basePathJs.'theme.min.js',
        'app' => $basePathJs.'app.min.js',
    ],
    // Danh sách các assets có thể load theo yêu cầu
    'optional' => [
        'css' => [
            'payments' => $basePathCss.'polirium-payments.min.css',
            'social' => $basePathCss.'polirium-social.min.css',
            'dropzone' => $libsPath.'dropzone/dist/dropzone.css',
        ],
        'js' => [
            'dropzone' => $libsPath.'dropzone/dist/dropzone-min.js',
        ],
    ],
];
