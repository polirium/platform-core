<?php

namespace Polirium\Core\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Polirium\Core\Settings\Support\SettingRegistry group(string $name, array $config = [])
 * @method static \Polirium\Core\Settings\Support\SettingRegistry add(string $key, array $config)
 * @method static array getGroups()
 * @method static array|null getGroup(string $name)
 * @method static array getGroupSettings(string $group)
 * @method static array|null getSetting(string $group, string $key)
 * @method static bool hasGroup(string $name)
 * @method static bool hasSetting(string $group, string $key)
 * @method static \Polirium\Core\Settings\Support\SettingRegistry removeGroup(string $name)
 * @method static \Polirium\Core\Settings\Support\SettingRegistry removeSetting(string $group, string $key)
 * @method static \Polirium\Core\Settings\Support\SettingRegistry clear()
 *
 * @see \Polirium\Core\Settings\Support\SettingRegistry
 */
class SettingRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'polirium:setting-registry';
    }
}
