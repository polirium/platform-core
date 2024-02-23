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

        foreach (BaseHelper::scanFolder(platform_path($type)) as $module) {
            $configuration = config(strtolower($type . '.' . $module . '.livewire'));

            if (! empty($configuration)) {
                foreach ($configuration as $key => $config) {
                    $components[$key] = $config;
                }
            }
        }

        return $components;
    }
}
