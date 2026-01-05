<?php

namespace Polirium\Core\Base\Traits;

use Illuminate\Support\Arr;
use Polirium\Core\Base\Helpers\BaseHelper;

trait GetMenuDataTrait
{
    protected function getAvailableMenus(): array
    {
        $menus = [];

        $configuration = config(strtolower('core-menu'));
        if (! empty($configuration)) {
            foreach ($configuration as $config) {
                $menus[$config['id']] = $config;
            }
        }

        $types = ['core', 'packages', 'modules'];

        foreach ($types as $type) {
            $menus = array_merge($menus, $this->getAvailableMenuForEachType($type));
        }

        // Note: Permission filtering is now done in Menu component render
        // because auth()->user() is not available during ServiceProvider boot
        return $menus;
    }

    protected function getAvailableMenuForEachType(string $type): array
    {
        $menus = [];

        foreach (BaseHelper::scanFolder(platform_path($type)) as $module) {
            $configuration = config(strtolower($type . '.' . $module . '.menu'));
            if (! empty($configuration)) {
                foreach ($configuration as $config) {
                    $menus[$config['id']] = $config;
                }
            }
        }

        return $menus;
    }

    protected function getMenuTree(array $menus): array
    {
        $sortedFlag = $menus;
        sort($sortedFlag);
        $children['root'] = $this->getChildren('root', $sortedFlag);

        foreach (array_keys($menus) as $key) {
            $childrenReturned = $this->getChildren($key, $menus);
            if (count($childrenReturned) > 0) {
                $children[$key] = $childrenReturned;
            }
        }

        return $children;
    }

    protected function getChildren(string $parentFlag, array $allFlags): array
    {
        $newFlagArray = [];
        foreach ($allFlags as $flagDetails) {
            if (Arr::get($flagDetails, 'parent', 'root') == $parentFlag) {
                $newFlagArray[] = [
                    'id' => $flagDetails['id'],
                    'sort' => ! empty($flagDetails['sort']) ? $flagDetails['sort'] : 0,
                ];
            }
        }

        $sorted =  array_values(Arr::sort($newFlagArray, function ($value) {
            return $value['sort'];
        }));

        return Arr::pluck($sorted, 'id');
    }
}
