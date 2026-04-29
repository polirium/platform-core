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

        $fileNames = array_merge($fileNames, ['livewire', 'menu', 'permissions']);

        foreach ($fileNames as $fileName) {
            if (file_exists($this->getConfigFilePath($fileName))) {
                $this->mergeConfigFrom($this->getConfigFilePath($fileName), $this->getDotedNamespace() . '.' . $fileName);
            }
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
        $namespace = $this->getDashedNamespace();
        $this->loadTranslationsFrom($this->getTranslationsPath(), $namespace);

        // Also register with dot notation for Laravel translator compatibility
        // e.g., core/base -> core.base
        $dotNamespace = $this->getDotedNamespace();
        if ($namespace !== $dotNamespace) {
            $this->loadTranslationsFrom($this->getTranslationsPath(), $dotNamespace);
        }

        // Reset any pre-cached empty translations for this namespace
        // This fixes the issue where trans() is called before namespace is registered,
        // causing empty cache entries that prevent real translations from loading
        $this->resetTranslatorCache($namespace);
        if ($namespace !== $dotNamespace) {
            $this->resetTranslatorCache($dotNamespace);
        }

        return $this;
    }

    /**
     * Reset translator loaded cache for a specific namespace.
     * This ensures fresh translations are loaded after namespace registration.
     */
    protected function resetTranslatorCache(string $namespace): void
    {
        $translator = $this->app['translator'];

        try {
            $loaded = (new \ReflectionProperty($translator, 'loaded'));
            $loaded->setAccessible(true);
            $loadedArr = $loaded->getValue($translator);

            // Remove cached entries for this namespace (they may be empty from early calls)
            if (isset($loadedArr[$namespace])) {
                unset($loadedArr[$namespace]);
                $loaded->setValue($translator, $loadedArr);
            }
        } catch (\ReflectionException $e) {
            // Silently ignore if reflection fails
        }
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
