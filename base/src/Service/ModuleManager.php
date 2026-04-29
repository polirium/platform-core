<?php

namespace Polirium\Core\Base\Service;

use Illuminate\Support\Facades\Artisan;
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

            if (! is_dir($moduleDir) || ! file_exists($composerFile)) {
                continue;
            }

            $composer = json_decode(file_get_contents($composerFile), true);
            if (! $composer) {
                continue;
            }

            // Get module info
            $moduleData = $this->parseModuleData($composer, $folder, $moduleDir);
            if (! $moduleData) {
                continue;
            }

            // Check if already in database
            $existing = Module::where('name', $folder)->first();
            if (! $existing) {
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
                    'author' => $moduleData['author'],
                    'image' => $moduleData['image'],
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
     * Parse module data from composer.json and module.json
     */
    protected function parseModuleData(array $composer, string $folder, string $path): ?array
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

        if (! $namespace || ! $provider) {
            return null;
        }

        // Parse Author from composer
        $author = 'Polyx'; // Default
        if (isset($composer['authors']) && is_array($composer['authors'])) {
            $authors = array_map(function ($a) {
                return $a['name'] ?? '';
            }, $composer['authors']);
            $author = implode(', ', array_filter($authors));
        }

        // Check for module.json
        $moduleJson = [];
        $moduleJsonFile = $path . '/module.json';
        if (file_exists($moduleJsonFile)) {
            $moduleJson = json_decode(file_get_contents($moduleJsonFile), true) ?? [];
        }

        return [
            'name' => $folder,
            'display_name' => $moduleJson['name'] ?? ($composer['description'] ?? ucfirst(str_replace('-', ' ', $folder))),
            'description' => $moduleJson['description'] ?? ($composer['description'] ?? null),
            'version' => $moduleJson['version'] ?? ($composer['version'] ?? '1.0.0'),
            'namespace' => $namespace,
            'provider' => $provider,
            'path' => $path,
            'status' => Module::STATUS_PENDING,
            'dependencies' => $composer['require'] ?? null,
            'author' => $moduleJson['author'] ?? $author,
            'image' => $moduleJson['image'] ?? "/admin/modules/{$folder}/image",
        ];
    }

    /**
     * Install a module (run migrations).
     */
    public function install(string $name): bool
    {
        $module = Module::where('name', $name)->first();
        if (! $module) {
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
    public function enable(string $name, bool $runMigrations = true): bool
    {
        $module = Module::where('name', $name)->first();

        if (! $module) {
            return false;
        }

        // If not installed (Pending), install or mark as installed first.
        if (! $module->isInstalled()) {
            if ($runMigrations) {
                if (! $this->install($name)) {
                    return false;
                }
            } else {
                $module->update(['status' => Module::STATUS_INSTALLED]);
            }
            $module->refresh();
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
        if (! $module) {
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
        if (! $module) {
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
        if (! \Schema::hasTable('modules')) {
            return;
        }

        $activeModules = $this->getActive();

        foreach ($activeModules as $module) {
            $this->loadModule($module);
        }
    }

    /**
     * Download module as ZIP.
     */
    public function download(string $name): ?string
    {
        $module = Module::where('name', $name)->first();
        if (! $module) {
            return null;
        }

        $modulePath = $module->path;
        if (! is_dir($modulePath)) {
            return null;
        }

        $fileName = $name . '-' . ($module->version ?? '1.0.0') . '.zip';
        $zipPath = storage_path('app/' . $fileName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($modulePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = $module->name . '/' . substr($filePath, strlen($modulePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

            return $zipPath;
        }

        return null;
    }
    /**
     * Delete a module safely (move to #delete folder).
     */
    public function delete(string $name): bool
    {
        $module = Module::where('name', $name)->first();
        if (! $module) {
            return false;
        }

        $modulePath = $module->path;
        if (! is_dir($modulePath)) {
            // If folder doesn't exist, just delete from DB
            $module->delete();

            return true;
        }

        // Create #delete directory if not exists
        $deleteDir = platform_path('modules/#delete');
        if (! is_dir($deleteDir)) {
            mkdir($deleteDir, 0755, true);
        }

        // Move module to #delete folder with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $newPath = $deleteDir . '/' . $name . '_' . $timestamp;

        if (rename($modulePath, $newPath)) {
            // Re-run discover to update DB/Cache
            $this->discover();

            return true;
        }

        return false;
    }
}
