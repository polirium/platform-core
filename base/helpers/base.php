<?php

use Polirium\Core\Base\Helpers\BaseHelper;

/**
 * |--------------------------------------------------------------------------
 * | Platform path helpers
 * |--------------------------------------------------------------------------
 * |
 */
if (! function_exists('platform_path')) {
    function platform_path(string $path = null): string
    {
        return base_path('platform/' . $path);
    }
}

if (! function_exists('core_path')) {
    function core_path(string $path = null): string
    {
        return platform_path('core/' . $path);
    }
}

if (! function_exists('modules_path')) {
    function modules_path(string $path = null): string
    {
        return platform_path('modules/' . $path);
    }
}

if (! function_exists('package_path')) {
    function package_path(string $path = null): string
    {
        return platform_path('packages/' . $path);
    }
}

if (! function_exists('admin_prefix')) {
    function admin_prefix(string $path = null): string
    {
        return BaseHelper::getAdminPrefix();
    }
}

if (! function_exists('core_can')) {
    function core_can(string $permissions): bool
    {
        if (auth()->user()->isSuperAdmin()) {
            return true;
        }

        return auth()->user()->can($permissions);
    }
}
