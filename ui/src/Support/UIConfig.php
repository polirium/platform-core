<?php

namespace Polirium\Core\UI\Support;

class UIConfig
{
    public const GLOBAL = 'global';

    protected static function mix(array $default, array $options): array
    {
        collect($options)->dot()->each(function ($value, $key) use (&$default) {
            data_set($default, $key, $value);
        });

        return $default;
    }
}
