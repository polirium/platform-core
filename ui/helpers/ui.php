<?php

use Polirium\Core\Settings\Facades\Settings;

/**
 * |--------------------------------------------------------------------------
 * | Platform assets helpers
 * |--------------------------------------------------------------------------
 * |
 */
if (! function_exists('render_css')) {
    function render_css()
    {
        return Assets::renderCss();
    }
}

if (! function_exists('render_js')) {
    function render_js()
    {
        return Assets::renderJs();
    }
}

if (! function_exists('get_title')) {
    function get_title()
    {
        return Settings::get('title', config('core.base.setting.title'));
    }
}

if (! function_exists('get_logo')) {
    function get_logo()
    {
        return Settings::get('logo', config('core.base.setting.logo'));
    }
}

if (! function_exists('get_wallpaper_auth')) {
    function get_wallpaper_auth()
    {
        $assets = [
            Assets::get('core/ui/assets/auth/auth-1.jpg'),
            Assets::get('core/ui/assets/auth/auth-2.jpg'),
            'https://picsum.photos/1280/853',
        ];

        $key = array_rand($assets);

        return $assets[$key];
    }
}
