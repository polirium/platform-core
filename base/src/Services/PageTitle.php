<?php

namespace Polirium\Core\Base\Services;

class PageTitle
{
    protected ?string $title = null;

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        if ($this->title) {
            return $this->title;
        }

        return $this->resolveFromMenu();
    }

    protected function resolveFromMenu(): ?string
    {
        // Logic to find title from current route in menu configuration
        // This is a naive implementation scanning configs directly as Menu Builder might not be ready or fully accessible here

        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
        if (!$currentRoute) {
            return null;
        }

        // We need to scan all loaded menus (core, modules, packages)
        // Since we don't have a centralized repository of raw menu arrays easily accessible without resolving providers,
        // we might rely on the Menu facade if available, or scan configs.

        // Let's try to find it in the Menu builder if 'core' menu exists
        // However, Menu items are populated later in the lifecycle usually.

        // Alternative: Scan configs manually
        $types = ['core', 'modules', 'packages'];
        foreach ($types as $type) {
            $path = platform_path($type);
            foreach (\Polirium\Core\Base\Helpers\BaseHelper::scanFolder($path) as $module) {
                $config = config(strtolower($type . '.' . $module . '.menu'));
                if (is_array($config)) {
                    $title = $this->findTitleInMenuConfig($config, $currentRoute);
                    if ($title) return $title;
                }
            }
        }

        return null;
    }

    protected function findTitleInMenuConfig(array $menuItems, string $currentRoute): ?string
    {
        foreach ($menuItems as $item) {
            if (isset($item['route']) && $item['route'] === $currentRoute) {
                return $item['name'];
            }
            // Note: Current menu config structure is flat list with 'parent' key, not recursive 'children' array
            // So simple iteration is enough for now.
        }
        return null;
    }
}
