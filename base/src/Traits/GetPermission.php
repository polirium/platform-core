<?php

namespace Polirium\Core\Base\Traits;

use Illuminate\Support\Arr;
use Polirium\Core\Base\Helpers\BaseHelper;

trait GetPermission
{
    protected function getAvailablePermissions(): array
    {
        $permissions = [];

        $configuration = config(strtolower('core-permissions'));
        if (! empty($configuration)) {
            foreach ($configuration as $config) {
                $permissions[$config['flag']] = $config;
            }
        }

        $types = ['core', 'packages', 'modules'];

        foreach ($types as $type) {
            $permissions = array_merge($permissions, $this->getAvailablePermissionForEachType($type));
        }

        return $permissions;
    }

    protected function getAvailablePermissionForEachType(string $type): array
    {
        $permissions = [];

        foreach (BaseHelper::scanFolder(platform_path($type)) as $module) {
            $configuration = config(strtolower($type . '.' . $module . '.permissions'));
            if (! empty($configuration)) {
                foreach ($configuration as $config) {
                    $permissions[$config['flag']] = $config;
                }
            }
        }

        return $permissions;
    }

    protected function getPermissionTree(array $permissions): array
    {
        $sortedFlag = $permissions;
        sort($sortedFlag);
        $children['root'] = $this->getChildren('root', $sortedFlag);

        foreach (array_keys($permissions) as $key) {
            $childrenReturned = $this->getChildren($key, $permissions);
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
            if (Arr::get($flagDetails, 'parent_flag', 'root') == $parentFlag) {
                $newFlagArray[] = $flagDetails['flag'];
            }
        }

        return $newFlagArray;
    }
}
