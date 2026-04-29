<?php

namespace Polirium\Core\UI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade cho quản lý Assets (CSS/JS)
 *
 * @method static string get(string $path) Lấy đường dẫn đầy đủ đến asset
 * @method static \Polirium\Core\UI\Support\Assets addCss(array $assets) Thêm CSS vào danh sách cơ bản
 * @method static \Polirium\Core\UI\Support\Assets addJs(array $assets) Thêm JS vào danh sách cơ bản
 * @method static \Polirium\Core\UI\Support\Assets addOptionalCss(array $assets) Thêm CSS tùy chọn
 * @method static \Polirium\Core\UI\Support\Assets addOptionalJs(array $assets) Thêm JS tùy chọn
 * @method static \Polirium\Core\UI\Support\Assets loadCss(string|array $names) Kích hoạt load CSS
 * @method static \Polirium\Core\UI\Support\Assets loadJs(string|array $names) Kích hoạt load JS
 * @method static \Illuminate\Support\HtmlString renderCss() Render tất cả CSS tags
 * @method static \Illuminate\Support\HtmlString renderJs() Render tất cả JS tags
 * @method static \Polirium\Core\UI\Support\Assets registerModuleAssets(string $module, array $assets) Đăng ký assets cho module
 * @method static bool has(string $name, string $type = 'js') Kiểm tra asset tồn tại
 * @method static string|null path(string $name, string $type = 'js') Lấy đường dẫn asset
 * @method static \Polirium\Core\UI\Support\Assets clearLoaded() Xóa cache loaded assets
 *
 * @see \Polirium\Core\UI\Support\Assets
 */
class Assets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'polirium:assets';
    }
}
