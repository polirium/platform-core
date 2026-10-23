<?php

namespace Polirium\Core\Base\Helpers;

use Illuminate\Support\Facades\File;

class BaseHelper
{
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');

        if (empty($helpers) || ! is_array($helpers)) {
            return;
        }

        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

    public static function scanFolder(string $path, array $ignoreFiles = []): array
    {
        if (File::isDirectory($path)) {
            $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
            natsort($data);

            return $data;
        }

        return [];
    }

    /**
     * Folder names of the modules, packages or core parts under platform/<type>.
     *
     * Only directories: platform/core also holds README.md, LICENSE and
     * composer.json. Looking for "LICENSE/config/…" is merely false on most
     * hosts, but with open_basedir enabled PHP raises a warning for it, which
     * fails every request.
     *
     * @return array<int, string>
     */
    public static function platformFolders(string $type): array
    {
        $root = platform_path($type);

        return array_values(array_filter(
            self::scanFolder($root),
            fn (string $name) => is_dir($root . DIRECTORY_SEPARATOR . $name)
        ));
    }

    public static function getAdminPrefix(): ?string
    {
        $prefix = config('core.base.setting.admin_dir');

        return $prefix;
    }
}
