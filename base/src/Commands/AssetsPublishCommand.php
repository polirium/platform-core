<?php

namespace Polirium\Core\Base\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('poli:assets:publish', 'Publish assets for all modules')]
class AssetsPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poli:assets:publish {--force : Force overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish assets for core, ui, base and modules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Publishing assets...');

        $force = $this->option('force');

        $this->call('vendor:publish', [
            '--provider' => 'Polirium\Core\UI\Providers\UIServiceProvider',
            '--force' => $force,
        ]);

        $this->components->info('Assets published successfully.');

        return self::SUCCESS;
    }
}
