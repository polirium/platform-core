<?php

use Polirium\Core\Settings\Facades\Settings;
use Polirium\Core\UI\Facades\Assets;

/**
 * |--------------------------------------------------------------------------
 * | Platform UI & Assets Helpers
 * |--------------------------------------------------------------------------
 * |
 * | Các helper functions cho việc quản lý Assets và UI
 * |
 */

/**
 * Render tất cả CSS tags (base + loaded)
 *
 * @return \Illuminate\Support\HtmlString
 */
if (! function_exists('render_css')) {
    function render_css()
    {
        return Assets::renderCss();
    }
}

/**
 * Render tất cả JS tags (base + loaded)
 *
 * @return \Illuminate\Support\HtmlString
 */
if (! function_exists('render_js')) {
    function render_js()
    {
        return Assets::renderJs();
    }
}

/**
 * Load CSS asset tùy chọn theo tên
 *
 * @param string|array $names Tên hoặc mảng tên các assets
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * load_css('dashboard');
 * load_css(['dashboard', 'chart']);
 */
if (! function_exists('load_css')) {
    function load_css(string|array $names)
    {
        return Assets::loadCss($names);
    }
}

/**
 * Load JS asset tùy chọn theo tên
 *
 * @param string|array $names Tên hoặc mảng tên các assets
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * load_js('sortable');
 * load_js(['sortable', 'dashboard']);
 */
if (! function_exists('load_js')) {
    function load_js(string|array $names)
    {
        return Assets::loadJs($names);
    }
}

/**
 * Kiểm tra asset có tồn tại không
 *
 * @param string $name Tên asset
 * @param string $type Loại (css|js)
 * @return bool
 *
 * @example
 * has_asset('dashboard', 'css') // true/false
 */
if (! function_exists('has_asset')) {
    function has_asset(string $name, string $type = 'js'): bool
    {
        return Assets::has($name, $type);
    }
}

/**
 * Lấy đường dẫn đầy đủ của asset
 *
 * @param string $path Đường dẫn tương đối
 * @return string
 *
 * @example
 * asset_path('core/ui/css/style.css')
 * // Return: https://example.com/vendor/polirium/core/ui/css/style.css
 */
if (! function_exists('asset_path')) {
    function asset_path(string $path): string
    {
        return Assets::get($path);
    }
}

/**
 * Lấy tiêu đề ứng dụng
 *
 * @return string
 */
if (! function_exists('get_title')) {
    function get_title()
    {
        $title = Settings::get('title', config('core.base.setting.title'));
        // If the title looks like an encrypted string (starts with eyJ), fall back to default
        if (str_starts_with($title, 'eyJ')) {
            return config('core.base.setting.title');
        }

        return $title;
    }
}

/**
 * Lấy logo ứng dụng
 *
 * @return string
 */
if (! function_exists('get_logo')) {
    function get_logo()
    {
        $logo = Settings::get('logo', config('core.base.setting.logo'));
        if ($logo && ! str_starts_with($logo, 'http') && ! str_starts_with($logo, '/')) {
            // Check if file exists in public/storage or public directly
            if (file_exists(public_path('storage/' . $logo))) {
                return asset('storage/' . $logo);
            }

            return asset($logo);
        }

        return $logo;
    }
}

/**
 * Lấy favicon ứng dụng
 *
 * @return string
 */
if (! function_exists('get_favicon')) {
    function get_favicon()
    {
        $favicon = Settings::get('favicon', config('core.base.setting.favicon'));
        if ($favicon && ! str_starts_with($favicon, 'http') && ! str_starts_with($favicon, '/')) {
            // Check if file exists in public/storage or public directly
            if (file_exists(public_path('storage/' . $favicon))) {
                return asset('storage/' . $favicon);
            }

            return asset($favicon);
        }

        return $favicon;
    }
}

/**
 * Kiểm tra chuỗi có chứa HTML không
 *
 * @param string $string Chuỗi cần kiểm tra
 * @return bool
 */
if (! function_exists('is_html')) {
    function is_html($string)
    {
        return preg_match('/<\s?[^\>]*\/?\s?>/i', $string);
    }
}

/**
 * Đăng ký assets cho module từ ServiceProvider
 *
 * @param string $module Tên module
 * @param array $assets Mảng assets
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * // Trong ModuleServiceProvider
 * register_module_assets('accounting', [
 *     'css' => ['css/accounting.css'],
 *     'optional' => [
 *         'js' => [
 *             'invoice' => 'js/invoice.js',
 *         ],
 *     ],
 * ]);
 */
if (! function_exists('register_module_assets')) {
    function register_module_assets(string $module, array $assets)
    {
        return Assets::registerModuleAssets($module, $assets);
    }
}

/**
 * Thêm CSS vào danh sách cơ bản
 *
 * @param array $assets Mảng assets
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * add_css(['custom' => 'modules/my/css/style.css']);
 */
if (! function_exists('add_css')) {
    function add_css(array $assets)
    {
        return Assets::addCss($assets);
    }
}

/**
 * Thêm JS vào danh sách cơ bản
 *
 * @param array $assets Mảng assets
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * add_js(['custom' => 'modules/my/js/script.js']);
 */
if (! function_exists('add_js')) {
    function add_js(array $assets)
    {
        return Assets::addJs($assets);
    }
}

/**
 * Thêm CSS tùy chọn
 *
 * @param array $assets Mảng assets ['key' => 'path']
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * add_optional_css([
 *     'chart' => 'libs/chartjs/chart.css',
 * ]);
 */
if (! function_exists('add_optional_css')) {
    function add_optional_css(array $assets)
    {
        return Assets::addOptionalCss($assets);
    }
}

/**
 * Thêm JS tùy chọn
 *
 * @param array $assets Mảng assets ['key' => 'path']
 * @return \Polirium\Core\UI\Support\Assets
 *
 * @example
 * add_optional_js([
 *     'chartjs' => 'libs/chartjs/chart.min.js',
 * ]);
 */
if (! function_exists('add_optional_js')) {
    function add_optional_js(array $assets)
    {
        return Assets::addOptionalJs($assets);
    }
}
