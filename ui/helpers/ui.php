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

if (! function_exists('get_favicon')) {
    function get_favicon()
    {
        return Settings::get('favicon', config('core.base.setting.favicon'));
    }
}

if (! function_exists('is_html')) {
    function is_html($string)
    {
        // Check if string contains any html tags.
        return preg_match('/<\s?[^\>]*\/?\s?>/i', $string);
    }
}
