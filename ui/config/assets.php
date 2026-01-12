<?php

/**
 * Assets Configuration for Polirium UI
 *
 * Relative paths from: public/vendor/polirium/
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Core CSS (always loaded)
    |--------------------------------------------------------------------------
    |
    | List of CSS files that will be loaded on every page
    |
    */
    'css' => [
        'core' => 'core/ui/css/polirium-core.min.css',
        'flags' => 'core/ui/css/polirium-flags.min.css',
        'vendors' => 'core/ui/css/polirium-vendors.min.css',
        'app' => 'core/ui/css/app.min.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Core JS (always loaded)
    |--------------------------------------------------------------------------
    |
    | List of JS files that will be loaded on every page
    |
    */
    'js' => [
        'polirium' => 'core/ui/js/polirium.min.js',
        'theme' => 'core/ui/js/theme.min.js',
        'app' => 'core/ui/js/app.min.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional Assets (loaded on demand)
    |--------------------------------------------------------------------------
    |
    | These assets are only loaded when loadCss() or loadJs() is called
    | Useful for features used only on certain pages
    |
    */

    'optional' => [
        /*
        |--------------------------------------------------------------------------
        | Optional CSS
        |--------------------------------------------------------------------------
        |
        | Example: 'dashboard' => 'core/base/css/dashboard.css'
        |
        | To load: Assets::loadCss('dashboard')
        | or in Blade: @php load_css('dashboard') @endphp
        |
        */
        'css' => [
            'payments' => 'core/ui/css/polirium-payments.min.css',
            'social' => 'core/ui/css/polirium-social.min.css',
            'dropzone' => 'core/ui/libs/dropzone/dist/dropzone.css',
            'dashboard' => 'core/base/css/dashboard.css',
        ],

        /*
        |--------------------------------------------------------------------------
        | Optional JS
        |--------------------------------------------------------------------------
        |
        | Example: 'chartjs' => 'core/ui/libs/chartjs/chart.min.js'
        |
        | To load: Assets::loadJs('chartjs')
        | or in Blade: @php load_js('chartjs') @endphp
        |
        */
        'js' => [
            'dropzone' => 'core/ui/libs/dropzone/dist/dropzone-min.js',
            'sortable' => 'core/base/js/vendor/sortable.min.js',
            'dashboard' => 'core/base/js/dashboard.js',
        ],
    ],
];
