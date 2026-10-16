<?php

namespace Polirium\Core\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Polirium\Core\Base\Http\Models\Module;
use Polirium\Core\Base\Service\ModuleManager;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('poli:install', 'Install Polirium ERP Core')]
class InstallCommand extends Command
{
    use ConfirmableTrait;

    protected const ROOT_MIGRATION_PATHS = [
        'database/migrations',
        'platform/core/base/database/migrations',
        'platform/core/settings/database/migrations',
        'platform/core/media/database/migrations',
        'platform/packages/vietnam-administrative-units/database/migrations',
    ];

    public function handle()
    {
        $this->components->info('Preparing install migrations...');
        $this->normalizeActivityLogMigrations();

        $this->components->info('Run core migration first (no module migrations)...');
        if ($this->call('migrate', [
            '--path' => self::ROOT_MIGRATION_PATHS,
            '--force' => true,
        ]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->components->info('Discover modules...');
        /** @var ModuleManager $moduleManager */
        $moduleManager = app(ModuleManager::class);
        $moduleManager->discover();

        $modulesToActivate = Module::pending()->orderBy('name')->pluck('name')->toArray();

        $this->components->info('Activate modules before module migrations...');
        foreach ($modulesToActivate as $moduleName) {
            $moduleManager->enable($moduleName, false);
            $this->line(" - Activated: {$moduleName}");
        }

        $activeModules = Module::active()->orderBy('name')->get();
        $moduleMigrationPaths = [];
        foreach ($activeModules as $module) {
            $migrationsPath = $module->path . '/database/migrations';
            if (is_dir($migrationsPath)) {
                $moduleMigrationPaths[] = $migrationsPath;
            }
        }

        if (! empty($moduleMigrationPaths)) {
            $this->components->info('Run migrations for active modules...');
            if ($this->call('migrate', [
                '--path' => $moduleMigrationPaths,
                '--realpath' => true,
                '--force' => true,
            ]) !== self::SUCCESS) {
                return self::FAILURE;
            }
        } else {
            $this->components->info('No active module migrations to run.');
        }

        $this->components->info('Synchronizing Vietnam administrative units...');
        if ($this->call('vietnam-units:sync', [
            '--apply' => true,
            '--archive-missing' => true,
        ]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->confirmToProceed('Create a new super user?', true)) {
            if ($this->call('poli:user:create') !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        $this->components->info('Installing ERP Core...');
        if ($this->call('vendor:publish', [
            '--provider' => 'Polirium\Core\UI\Providers\UIServiceProvider',
            '--force' => true,
        ]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->components->info('Install completed.');

        return self::SUCCESS;
    }

    protected function normalizeActivityLogMigrations(): void
    {
        $duplicatedFiles = glob(base_path('database/migrations/*_add_event_column_to_activity_log_table.php')) ?: [];
        foreach ($duplicatedFiles as $filePath) {
            File::delete($filePath);
        }

        $duplicatedBatchFiles = glob(base_path('database/migrations/*_add_batch_uuid_column_to_activity_log_table.php')) ?: [];
        foreach ($duplicatedBatchFiles as $filePath) {
            File::delete($filePath);
        }
    }
}
