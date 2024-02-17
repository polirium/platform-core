<?php

namespace Polirium\Core\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('poli:install', 'Install Polirium ERP Core')]
class InstallCommand extends Command
{
    use ConfirmableTrait;

    public function handle()
    {
        $this->components->info('Installing vendor publish...');
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
            '--tag' => 'activitylog-migrations',
            '--force' => true,
        ]);

        $this->components->info('Run migration...');
        $this->call('migrate');

        if ($this->confirmToProceed('Create a new super user?', true)) {
            $this->call('poli:user:create');
        }

        $this->components->info('Installing ERP Core...');
        $this->call('vendor:publish', [
            '--provider' => 'Polirium\Core\UI\Providers\UIServiceProvider',
            '--force' => true,
        ]);
    }
}
