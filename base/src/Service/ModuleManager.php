<?php

namespace Polirium\Core\Base\Service;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Polirium\Core\Base\Helpers\BaseHelper;
use Polirium\Core\Base\Http\Models\Module;

class ModuleManager
{
    protected string $modulesPath;

    public function __construct()
    {
        $this->modulesPath = platform_path('modules');
    }

    /**
     * Discover modules from filesystem and sync with database.
     */
    public function discover(): array
    {
        $discovered = [];
        $folders = BaseHelper::scanFolder($this->modulesPath);

        foreach ($folders as $folder) {
            $moduleDir = $this->modulesPath . '/' . $folder;
            $composerFile = $moduleDir . '/composer.json';

            if (!is_dir($moduleDir) || !file_exists($composerFile)) {
                continue;
            }

            $composer = json_decode(file_get_contents($composerFile), true);
            if (!$composer) {
                continue;
            }

            // Get module info
            $moduleData = $this->parseComposerJson($composer, $folder, $moduleDir);
            if (!$moduleData) {
                continue;
            }

            // Check if already in database
            $existing = Module::where('name', $folder)->first();
            if (!$existing) {
                // Add new discovered module
                $module = Module::create($moduleData);
                $discovered[] = $module;
            } else {
                // Update existing module info (except status)
                $existing->update([
                    'display_name' => $moduleData['display_name'],
                    'description' => $moduleData['description'],
                    'version' => $moduleData['version'],
                    'namespace' => $moduleData['namespace'],
                    'provider' => $moduleData['provider'],
                    'path' => $moduleData['path'],
                    'dependencies' => $moduleData['dependencies'],
                ]);
                $discovered[] = $existing;
            }
        }

        // Remove modules from DB that no longer exist in filesystem
        $existingNames = collect($discovered)->pluck('name')->toArray();
        Module::whereNotIn('name', $existingNames)->delete();

        return $discovered;
    }

    /**
     * Parse composer.json to extract module info.
     */
    protected function parseComposerJson(array $composer, string $folder, string $path): ?array
    {
        // Get PSR-4 namespace
        $namespace = null;
        if (isset($composer['autoload']['psr-4'])) {
            $namespace = array_key_first($composer['autoload']['psr-4']);
        }

        // Get provider
        $provider = null;
        if (isset($composer['extra']['laravel']['providers'][0])) {
            $provider = $composer['extra']['laravel']['providers'][0];
        }

        if (!$namespace || !$provider) {
            return null;
        }

        return [
            'name' => $folder,
            'display_name' => $composer['description'] ?? ucfirst(str_replace('-', ' ', $folder)),
            'description' => $composer['description'] ?? null,
            'version' => $composer['version'] ?? '1.0.0',
            'namespace' => $namespace,
            'provider' => $provider,
            'path' => $path,
            'status' => Module::STATUS_PENDING,
            'dependencies' => $composer['require'] ?? null,
        ];
    }

    /**
     * Install a module (run migrations).
     */
    public function install(string $name): bool
    {
        $module = Module::where('name', $name)->first();
        if (!$module) {
            return false;
        }

        // Run migrations
        $migrationsPath = $module->path . '/database/migrations';
        if (is_dir($migrationsPath)) {
            Artisan::call('migrate', [
                '--path' => str_replace(base_path() . '/', '', $migrationsPath),
                '--force' => true,
            ]);
        }

        // Update status
        $module->update(['status' => Module::STATUS_INSTALLED]);

        return true;
    }

    /**
     * Enable a module.
     */
    public function enable(string $name): bool
    {
        $module = Module::where('name', $name)->first();
        if (!$module || !$module->isInstalled()) {
            return false;
        }

        $module->update(['status' => Module::STATUS_ACTIVE]);

        // Clear caches
        Artisan::call('optimize:clear');

        return true;
    }

    /**
     * Disable a module.
     */
    public function disable(string $name): bool
    {
        $module = Module::where('name', $name)->first();
        if (!$module) {
            return false;
        }

        $module->update(['status' => Module::STATUS_DISABLED]);

        // Clear caches
        Artisan::call('optimize:clear');

        return true;
    }

    /**
     * Uninstall a module (reset status, optionally rollback migrations).
     */
    public function uninstall(string $name, bool $rollbackMigrations = false): bool
    {
        $module = Module::where('name', $name)->first();
        if (!$module) {
            return false;
        }

        // Rollback migrations if requested
        if ($rollbackMigrations) {
            $migrationsPath = $module->path . '/database/migrations';
            if (is_dir($migrationsPath)) {
                Artisan::call('migrate:rollback', [
                    '--path' => str_replace(base_path() . '/', '', $migrationsPath),
                    '--force' => true,
                ]);
            }
        }

        $module->update(['status' => Module::STATUS_PENDING]);

        // Clear caches
        Artisan::call('optimize:clear');

        return true;
    }

    /**
     * Get all active modules.
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Module::active()->get();
    }

    /**
     * Get all modules.
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Module::orderBy('display_name')->get();
    }

    /**
     * Register autoloaders and providers for a module.
     */
    public function loadModule(Module $module): void
    {
        // Register PSR-4 autoloader
        spl_autoload_register(function ($class) use ($module) {
            $namespace = $module->namespace;
            if (strpos($class, $namespace) === 0) {
                $relativeClass = substr($class, strlen($namespace));
                $file = $module->path . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });

        // Register provider
        if (class_exists($module->provider)) {
            app()->register($module->provider);
        }
    }

    /**
     * Load all active modules.
     */
    public function loadActiveModules(): void
    {
        // Check if table exists
        if (!\Schema::hasTable('modules')) {
            return;
        }

        $activeModules = $this->getActive();

        foreach ($activeModules as $module) {
            $this->loadModule($module);
        }
    }
}
