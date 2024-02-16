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

    public static function getAdminPrefix(): string
    {
        $prefix = config('core.base.setting.admin_dir');

        return $prefix;
    }
}
