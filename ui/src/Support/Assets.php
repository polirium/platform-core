<?php

namespace Polirium\Core\UI\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Assets
{
    /**
     * Đã load config chưa
     */
    protected bool $configLoaded = false;

    /**
     * Các assets cơ bản (luôn được load)
     */
    protected array $css = [];

    protected array $js = [];

    /**
     * Các assets tùy chọn (chỉ load khi được yêu cầu)
     */
    protected array $optionalCss = [];

    protected array $optionalJs = [];

    /**
     * Danh sách các assets tùy chọn đã được kích hoạt để load
     */
    protected array $loadedOptionalCss = [];

    protected array $loadedOptionalJs = [];

    /**
     * Đường dẫn base cho assets
     */
    protected string $basePath = 'vendor/polirium';

    public function __construct()
    {
        // Don't load config in constructor - lazy load when needed
    }

    /**
     * Load configuration from config file (lazy load)
     */
    protected function loadConfig(): void
    {
        if ($this->configLoaded) {
            return;
        }

        $config = config('core.ui.assets', []);

        $this->css = $config['css'] ?? [];
        $this->js = $config['js'] ?? [];
        $this->optionalCss = $config['optional']['css'] ?? [];
        $this->optionalJs = $config['optional']['js'] ?? [];

        $this->configLoaded = true;
    }

    /**
     * Load assets from modules (via PackageManifest)
     *
     * @note: Reserved for future use when modules declare assets in composer.json
     * Currently modules should register assets in their ServiceProvider
     */
    protected function loadModuleAssets(): void
    {
        // TODO: Auto-discover assets from module composer.json
        // Currently modules should register assets in their ServiceProvider

        // Example composer.json structure:
        // "extra": {
        //     "polirium": {
        //         "assets": {
        //             "css": ["css/module.css"],
        //             "optional": {
        //                 "js": ["feature" => "js/feature.js"]
        //             }
        //         }
        //     }
        // }
    }

    /**
     * Get full URL for an asset
     *
     * @param string $path Relative path to asset
     * @return string Full URL to asset
     */
    public function get(string $path): string
    {
        $assetPath = $this->basePath . '/' . ltrim($path, '/');

        return asset($assetPath);
    }

    /**
     * Add CSS to base assets (always loaded)
     *
     * @param array $assets Array of assets ['key' => 'path'] or ['path1', 'path2']
     * @return static
     *
     * @example
     * // Add with key
     * Assets::addCss(['custom' => 'modules/my/css/custom.css'])
     *
     * // Add multiple (auto-generate key)
     * Assets::addCss(['modules/my/css/style.css', 'modules/my/css/theme.css'])
     */
    public function addCss(array $assets): static
    {
        $this->loadConfig();
        $this->css = array_merge($this->css, $this->normalizeAssets($assets));

        return $this;
    }

    /**
     * Thêm JS vào danh sách JS cơ bản
     *
     * @param array $assets Mảng assets ['key' => 'path'] hoặc ['path1', 'path2']
     * @return static
     *
     * @example
     * Assets::addJs(['custom' => 'modules/my/js/custom.js'])
     */
    public function addJs(array $assets): static
    {
        $this->loadConfig();
        $this->js = array_merge($this->js, $this->normalizeAssets($assets));

        return $this;
    }

    /**
     * Chuẩn hóa mảng assets để đảm bảo có key
     */
    protected function normalizeAssets(array $assets): array
    {
        $normalized = [];

        foreach ($assets as $key => $value) {
            if (is_int($key)) {
                // Nếu key là số, tự sinh key từ path
                $normalized[Str::slug(basename($value, '.css'), '_') . '_' . Str::random(4)] = $value;
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * Add optional CSS (only loaded when requested)
     *
     * @param array $assets Array of assets ['key' => 'path']
     * @return static
     *
     * @example
     * // In ServiceProvider
     * Assets::addOptionalCss([
     *     'dashboard' => 'modules/dashboard/css/dashboard.css',
     *     'chart' => 'core/ui/libs/chartjs/chart.css',
     * ]);
     */
    public function addOptionalCss(array $assets): static
    {
        $this->loadConfig();
        $this->optionalCss = array_merge($this->optionalCss, $assets);

        return $this;
    }

    /**
     * Add optional JS (only loaded when requested)
     *
     * @param array $assets Array of assets ['key' => 'path']
     * @return static
     *
     * @example
     * Assets::addOptionalJs([
     *     'chartjs' => 'core/ui/libs/chartjs/chart.min.js',
     *     'sortable' => 'core/base/js/vendor/sortable.min.js',
     * ]);
     */
    public function addOptionalJs(array $assets): static
    {
        $this->loadConfig();
        $this->optionalJs = array_merge($this->optionalJs, $assets);

        return $this;
    }

    /**
     * Activate CSS loading (mark for rendering)
     *
     * @param string|array $names Asset name or array of names
     * @return static
     *
     * @example
     * // In Blade view
     * @php
     *     Assets::loadCss('dashboard');
     *     // or load multiple
     *     Assets::loadCss(['dashboard', 'chart']);
     * @endphp
     */
    public function loadCss(string|array $names): static
    {
        $this->loadConfig();
        $names = is_array($names) ? $names : [$names];

        foreach ($names as $name) {
            if (isset($this->optionalCss[$name]) && ! in_array($name, $this->loadedOptionalCss)) {
                $this->loadedOptionalCss[] = $name;
            }
        }

        return $this;
    }

    /**
     * Activate JS loading (mark for rendering)
     *
     * @param string|array $names Asset name or array of names
     * @return static
     *
     * @example
     * Assets::loadJs(['sortable', 'dashboard']);
     */
    public function loadJs(string|array $names): static
    {
        $this->loadConfig();
        $names = is_array($names) ? $names : [$names];

        foreach ($names as $name) {
            if (isset($this->optionalJs[$name]) && ! in_array($name, $this->loadedOptionalJs)) {
                $this->loadedOptionalJs[] = $name;
            }
        }

        return $this;
    }

    /**
     * Render all CSS tags (base + loaded optional)
     */
    public function renderCss(): HtmlString
    {
        $this->loadConfig();
        $html = '';

        // Merge base CSS + loaded optional CSS
        $allCss = array_merge($this->css, $this->getLoadedOptionalCss());

        foreach ($allCss as $css) {
            $html .= sprintf('<link href="%s" rel="stylesheet" />', $this->get($css)) . PHP_EOL;
        }

        return new HtmlString($html);
    }

    /**
     * Render all JS tags (base + loaded optional)
     */
    public function renderJs(): HtmlString
    {
        $this->loadConfig();
        $html = '';

        // Merge base JS + loaded optional JS
        $allJs = array_merge($this->js, $this->getLoadedOptionalJs());

        foreach ($allJs as $js) {
            $html .= sprintf('<script src="%s"></script>', $this->get($js)) . PHP_EOL;
        }

        return new HtmlString($html);
    }

    /**
     * Get list of loaded optional CSS
     */
    protected function getLoadedOptionalCss(): array
    {
        $css = [];

        foreach ($this->loadedOptionalCss as $name) {
            if (isset($this->optionalCss[$name])) {
                $css[$name] = $this->optionalCss[$name];
            }
        }

        return $css;
    }

    /**
     * Get list of loaded optional JS
     */
    protected function getLoadedOptionalJs(): array
    {
        $js = [];

        foreach ($this->loadedOptionalJs as $name) {
            if (isset($this->optionalJs[$name])) {
                $js[$name] = $this->optionalJs[$name];
            }
        }

        return $js;
    }

    /**
     * Add assets from module (legacy method - backward compatibility)
     *
     * @param array $assets Array of assets ['css' => [], 'js' => [], 'optional' => ['css' => [], 'js' => []]]
     * @return static
     *
     * @deprecated Use addCss(), addJs(), addOptionalCss(), addOptionalJs() directly instead
     */
    public function addModuleAssets(array $assets): static
    {
        if (isset($assets['css']) && is_array($assets['css'])) {
            $this->addCss($assets['css']);
        }

        if (isset($assets['js']) && is_array($assets['js'])) {
            $this->addJs($assets['js']);
        }

        if (isset($assets['optional']['css']) && is_array($assets['optional']['css'])) {
            $this->addOptionalCss($assets['optional']['css']);
        }

        if (isset($assets['optional']['js']) && is_array($assets['optional']['js'])) {
            $this->addOptionalJs($assets['optional']['js']);
        }

        return $this;
    }

    /**
     * Register assets for a module
     *
     * @param string $module Module name (e.g., 'accounting', 'product')
     * @param array $assets Array of assets
     * @return static
     *
     * @example
     * Assets::registerModuleAssets('accounting', [
     *     'css' => ['modules/accounting/css/accounting.css'],
     *     'optional' => [
     *         'js' => ['invoice' => 'modules/accounting/js/invoice.js'],
     *     ],
     * ]);
     */
    public function registerModuleAssets(string $module, array $assets): static
    {
        // Add module prefix to paths if not present
        $assets = $this->prefixModulePaths($module, $assets);

        return $this->addModuleAssets($assets);
    }

    /**
     * Add module prefix to asset paths
     */
    protected function prefixModulePaths(string $module, array $assets): array
    {
        $prefix = 'modules/' . $module . '/';

        $prefixPath = function (&$array) use ($prefix) {
            foreach ($array as $key => $path) {
                if (! str_starts_with($path, 'core/') && ! str_starts_with($path, 'modules/')) {
                    $array[$key] = $prefix . ltrim($path, '/');
                }
            }
        };

        if (isset($assets['css'])) {
            $prefixPath($assets['css']);
        }

        if (isset($assets['js'])) {
            $prefixPath($assets['js']);
        }

        if (isset($assets['optional']['css'])) {
            $prefixPath($assets['optional']['css']);
        }

        if (isset($assets['optional']['js'])) {
            $prefixPath($assets['optional']['js']);
        }

        return $assets;
    }

    /**
     * Check if asset exists
     *
     * @param string $name Asset name
     * @param string $type Type (css|js)
     * @return bool
     */
    public function has(string $name, string $type = 'js'): bool
    {
        $this->loadConfig();
        $array = $type === 'css' ? $this->optionalCss : $this->optionalJs;

        return isset($array[$name]);
    }

    /**
     * Get asset path
     *
     * @param string $name Asset name
     * @param string $type Type (css|js)
     * @return string|null
     */
    public function path(string $name, string $type = 'js'): ?string
    {
        $this->loadConfig();
        $array = $type === 'css' ? $this->optionalCss : $this->optionalJs;

        return $array[$name] ?? null;
    }

    /**
     * Clear loaded assets cache
     */
    public function clearLoaded(): static
    {
        $this->loadedOptionalCss = [];
        $this->loadedOptionalJs = [];

        return $this;
    }
}
