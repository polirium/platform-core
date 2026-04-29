<?php

namespace Polirium\Core\DevTool\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Polirium\Core\DevTool\Commands\Abstracts\BaseMakeCommand;
use Polirium\Core\DevTool\Helper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('poli:module:create', 'Create a new module.')]
class ModulesCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $module = strtolower($this->argument('name'));
        $location = modules_path($module);

        if ($this->laravel['files']->isDirectory($location)) {
            $this->components->error(sprintf('A module named [%s] already exists.', $module));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        $this->renameFiles($module, $location);
        $this->searchAndReplaceInFiles($module, $location);

        $this->components->info(
            sprintf('<info>The module</info> <comment>%s</comment> <info>was created in</info> <comment>%s</comment><info>, customize it!</info>', $module, $location)
        );
        $this->components->info(
            sprintf('<info>Add</info> <comment>"polirium/%s": "*@dev"</comment> to composer.json then run <comment>composer update</comment> to install this module!', $module)
        );

        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return dirname(__DIR__, 2) .
            DIRECTORY_SEPARATOR .
            Helper::joinPaths(['stubs', 'module']);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{-module}' => strtolower($replaceText),
            '{module}' => Str::snake(str_replace('-', '_', $replaceText)),
            '{+module}' => Str::camel($replaceText),
            '{modules}' => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),
            '{Modules}' => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $replaceText)))),
            '{-modules}' => Str::plural($replaceText),
            '{MODULE}' => strtoupper(Str::snake(str_replace('-', '_', $replaceText))),
            '{Module}' => str($replaceText)
                ->replace('/', '\\')
                ->afterLast('\\')
                ->studly()
                ->prepend('Polirium\\Modules\\'),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The modules name that you want to create');
    }
}
