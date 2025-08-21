<?php

namespace Polirium\Core\UI\Support;

use Illuminate\Support\HtmlString;

class Assets
{
    protected $css = [];

    protected $js = [];

    protected $optionalCss = [];

    protected $optionalJs = [];

    protected $loadedOptionalCss = [];

    protected $loadedOptionalJs = [];

    public function __construct()
    {
        $this->css = $this->getOption('css', []);
        $this->js = $this->getOption('js', []);
        $this->optionalCss = $this->getOption('optional.css', []);
        $this->optionalJs = $this->getOption('optional.js', []);
    }

    public function getOption(string $key, string|array $default = null): string|array
    {
        return config('core.ui.assets.' . $key, $default);
    }

    public function get(?string $path): string
    {
        return asset('vendor/polirium/' . $path);
    }

    public function addCss(array $assets): self
    {
        $this->css = array_merge($this->css, $assets);

        return $this;
    }

    public function addJs(array $assets): self
    {
        $this->js = array_merge($this->js, $assets);

        return $this;
    }

    /**
     * Load một CSS asset tùy chọn theo tên
     *
     * @param string|array $names Tên asset hoặc mảng các tên
     * @return self
     */
    public function loadCss(string|array $names): self
    {
        $names = is_array($names) ? $names : [$names];

        foreach ($names as $name) {
            if (isset($this->optionalCss[$name]) && ! in_array($name, $this->loadedOptionalCss)) {
                $this->loadedOptionalCss[] = $name;
            }
        }

        return $this;
    }

    /**
     * Load một JS asset tùy chọn theo tên
     *
     * @param string|array $names Tên asset hoặc mảng các tên
     * @return self
     */
    public function loadJs(string|array $names): self
    {
        $names = is_array($names) ? $names : [$names];

        foreach ($names as $name) {
            if (isset($this->optionalJs[$name]) && ! in_array($name, $this->loadedOptionalJs)) {
                $this->loadedOptionalJs[] = $name;
            }
        }

        return $this;
    }

    public function renderCss(): HtmlString
    {
        $cssBase = $this->css;
        $html = '';

        // Thêm các CSS tùy chọn đã được load
        $optionalCss = [];
        foreach ($this->loadedOptionalCss as $name) {
            if (isset($this->optionalCss[$name])) {
                $optionalCss[$name] = $this->optionalCss[$name];
            }
        }

        $allCss = array_merge($cssBase, $optionalCss);

        foreach ($allCss as $css) {
            $html .= "<link href='" . $this->get($css) . "' rel='stylesheet' />";
        }

        return new HtmlString($html);
    }

    public function renderJs(): HtmlString
    {
        $jsBase = $this->js;
        $html = '';

        // Thêm các JS tùy chọn đã được load
        $optionalJs = [];
        foreach ($this->loadedOptionalJs as $name) {
            if (isset($this->optionalJs[$name])) {
                $optionalJs[$name] = $this->optionalJs[$name];
            }
        }

        $allJs = array_merge($jsBase, $optionalJs);

        foreach ($allJs as $js) {
            $html .= "<script src='" . $this->get($js) . "'></script>";
        }

        return new HtmlString($html);
    }

    /**
     * Thêm optional CSS assets
     *
     * @param array $assets Mảng assets [key => path]
     * @return self
     */
    public function addOptionalCss(array $assets): self
    {
        $this->optionalCss = array_merge($this->optionalCss, $assets);

        return $this;
    }

    /**
     * Thêm optional JS assets
     *
     * @param array $assets Mảng assets [key => path]
     * @return self
     */
    public function addOptionalJs(array $assets): self
    {
        $this->optionalJs = array_merge($this->optionalJs, $assets);

        return $this;
    }

    /**
     * Thêm assets từ module khác
     *
     * @param array $assets Mảng assets theo cấu trúc ['css' => [], 'js' => [], 'optional' => ['css' => [], 'js' => []]]
     * @return self
     */
    public function addModuleAssets(array $assets): self
    {
        // Thêm CSS cơ bản
        if (isset($assets['css']) && is_array($assets['css'])) {
            $this->addCss($assets['css']);
        }

        // Thêm JS cơ bản
        if (isset($assets['js']) && is_array($assets['js'])) {
            $this->addJs($assets['js']);
        }

        // Thêm optional CSS
        if (isset($assets['optional']['css']) && is_array($assets['optional']['css'])) {
            $this->addOptionalCss($assets['optional']['css']);
        }

        // Thêm optional JS
        if (isset($assets['optional']['js']) && is_array($assets['optional']['js'])) {
            $this->addOptionalJs($assets['optional']['js']);
        }

        return $this;
    }
}
