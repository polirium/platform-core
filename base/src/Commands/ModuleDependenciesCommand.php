<?php

namespace Polirium\Core\Base\Commands;

use Illuminate\Console\Command;

class ModuleDependenciesCommand extends Command
{
    protected $signature = 'poli:module:dependencies {--install : Install dependencies}';
    protected $description = 'Manage module dependencies';

    public function handle()
    {
        $modulePath = base_path('platform/modules');
        $modules = array_diff(scandir($modulePath), ['.', '..']);

        foreach ($modules as $module) {
            $moduleDir = $modulePath . '/' . $module;
            $composerFile = $moduleDir . '/composer.json';

            if (is_dir($moduleDir) && file_exists($composerFile)) {
                $this->info("Processing module: {$module}");

                if ($this->option('install')) {
                    $this->installDependencies($moduleDir);
                } else {
                    $this->showDependencies($composerFile);
                }
            }
        }
    }

    protected function installDependencies($moduleDir)
    {
        $composer = json_decode(file_get_contents($moduleDir . '/composer.json'), true);

        if (isset($composer['require']) && ! empty($composer['require'])) {
            $packages = array_keys($composer['require']);

            $this->info('Installing dependencies for module...');
            $command = 'composer require ' . implode(' ', $packages);

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->info('Dependencies installed successfully!');
            } else {
                $this->error('Failed to install dependencies');
            }
        }
    }

    protected function showDependencies($composerFile)
    {
        $composer = json_decode(file_get_contents($composerFile), true);

        if (isset($composer['require']) && ! empty($composer['require'])) {
            $this->table(
                ['Package', 'Version'],
                collect($composer['require'])->map(function ($version, $package) {
                    return [$package, $version];
                })->toArray()
            );
        } else {
            $this->info('No dependencies found');
        }
    }
}
