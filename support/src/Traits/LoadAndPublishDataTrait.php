<?php

namespace Polirium\Core\Support\Traits;

trait LoadAndPublishDataTrait
{
    protected $namespace = null;

    protected $basePath = null;

    public function setNamespace(string $namespace): self
    {
        $this->namespace = ltrim(rtrim($namespace, '/'), '/');

        return $this;
    }

    public function loadConfigurations($fileNames): self
    {
        if (! is_array($fileNames)) {
            $fileNames = [$fileNames];
        }
        foreach ($fileNames as $fileName) {
            $this->mergeConfigFrom($this->getConfigFilePath($fileName), $this->getDotedNamespace() . '.' . $fileName);
        }

        return $this;
    }

    protected function getConfigFilePath($file): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/config/' . $file . '.php';
    }

    public function getBasePath(): string
    {
        return $this->basePath ?? platform_path();
    }

    public function setBasePath($path): self
    {
        $this->basePath = $path;

        return $this;
    }

    protected function getDashedNamespace(): string
    {
        return str_replace('.', '/', $this->namespace);
    }

    protected function getDotedNamespace(): string
    {
        return str_replace('/', '.', $this->namespace);
    }

    public function loadRoutes($fileNames = ['web']): self
    {
        if (! is_array($fileNames)) {
            $fileNames = [$fileNames];
        }

        foreach ($fileNames as $fileName) {
            $this->loadRoutesFrom($this->getRouteFilePath($fileName));
        }

        return $this;
    }

    protected function getRouteFilePath($file): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/routes/' . $file . '.php';
    }

    public function loadViews(): self
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->getDashedNamespace());

        return $this;
    }

    protected function getViewsPath(): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/resources/views/';
    }

    public function loadTranslations(): self
    {
        $this->loadTranslationsFrom($this->getTranslationsPath(), $this->getDashedNamespace());

        return $this;
    }

    protected function getTranslationsPath(): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/resources/lang/';
    }

    public function loadMigrations(): self
    {
        $this->loadMigrationsFrom($this->getMigrationsPath());

        return $this;
    }

    protected function getMigrationsPath(): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/database/migrations/';
    }

    public function publishAssets($path = null): self
    {
        if ($this->app->runningInConsole()) {
            if (empty($path)) {
                $path = 'vendor/polirium/' . $this->getDashedNamespace();
            }
            $this->publishes([$this->getAssetsPath() => public_path($path)], 'crm-public');
        }

        return $this;
    }

    protected function getAssetsPath(): string
    {
        return $this->getBasePath() . $this->getDashedNamespace() . '/public/';
    }
}
