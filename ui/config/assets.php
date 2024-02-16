<?php

$basePathCss = 'core/ui/css/';
$basePathJs = 'core/ui/js/';

return [
    'css' => [
        'core' => $basePathCss.'polirium-core.min.css',
        'flags' => $basePathCss.'polirium-flags.min.css',
        'payments' => $basePathCss.'polirium-payments.min.css',
        'vendors' => $basePathCss.'polirium-vendors.min.css',
        'app' => $basePathCss.'app.min.css',
    ],
    'js' => [
        'polirium' => $basePathJs.'polirium.min.js',
        'theme' => $basePathJs.'theme.min.js',
        'app' => $basePathJs.'app.min.js',
    ],
];
