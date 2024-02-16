<?php

namespace Polirium\Core\UI\Support;

use Illuminate\Support\HtmlString;

class Assets
{
    protected $css = [];

    protected $js = [];

    public function __construct()
    {
        $this->css = $this->getOption('css', []);
        $this->js = $this->getOption('js', []);
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

    public function renderCss(): HtmlString
    {
        $cssBase = $this->css;
        $html = '';
        foreach ($cssBase as $css) {
            $html .= "<link href='" . $this->get($css) . "' rel='stylesheet' />";
        }

        return new HtmlString($html);
    }

    public function renderJs(): HtmlString
    {
        $jsBase = $this->js;
        $html = '';
        foreach ($jsBase as $js) {
            $html .= "<script src='" . $this->get($js) . "'></script>";
        }

        return new HtmlString($html);
    }
}
