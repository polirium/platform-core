<?php

use Polirium\Core\Settings\Settings;
use Polirium\Core\Settings\Support\Context;

if (! function_exists('settings')) {
    function settings($key = null, $default = null, $context = null)
    {
        $settings = app(Settings::class);
        if ($key === null) {
            return $settings;
        }
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                if ($context instanceof Context) {
                    $settings->context($context);
                }

                $settings->set(key: $name, value: $value);
            }

            return null;
        }

        if ($context instanceof Context || is_bool($context)) {
            $settings->context($context);
        }

        return $settings->get(key: $key, default: $default);
    }
}
