<?php

namespace Polirium\Core\Base\Traits;

use Polirium\Core\Base\Helpers\BaseHelper;

trait LivewireComponentsTrait
{
    protected function getLivewireComponent(): array
    {
        $menus = [];

        $types = ['core', 'packages', 'modules'];

        foreach ($types as $type) {
            $menus = array_merge($menus, $this->getComponentForEachType($type));
        }

        return $menus;
    }

    protected function getComponentForEachType(string $type): array
    {
        $components = [];

        // Without a modules table (or a database at all, on first install) every module is listed.
        $activeModules = $type === 'modules' ? \Polirium\Core\Base\Service\ModuleManager::activeModuleNames() : null;
        $checkActive = $activeModules !== null;

        foreach (BaseHelper::scanFolder(platform_path($type)) as $module) {
            if ($checkActive && ! in_array($module, $activeModules)) {
                continue;
            }

            $configuration = [];

            // Always read from file first to get latest components
            $configFile = platform_path($type . '/' . $module . '/config/livewire.php');
            if (file_exists($configFile)) {
                $configuration = require $configFile;
            }

            // Fallback to config if file doesn't exist
            if (empty($configuration)) {
                $key = strtolower($type . '.' . $module . '.livewire');
                $configuration = config($key, []);
            }

            if (! empty($configuration)) {
                foreach ($configuration as $alias => $config) {
                    $components[$alias] = $config;
                }
            }
        }

        return $components;
    }
}
