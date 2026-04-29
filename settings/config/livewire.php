<?php

use Polirium\Core\Settings\Http\Livewire\DynamicSettings;
use Polirium\Core\Settings\Http\Livewire\GeneralSettings;

return [
    'general-settings' => [
        'class' => GeneralSettings::class,
        'alias' => 'core.settings.general-settings',
        'description' => 'General Settings Component',
    ],
    'dynamic-settings' => [
        'class' => DynamicSettings::class,
        'alias' => 'core.settings.dynamic-settings',
        'description' => 'Dynamic Settings Component',
    ],
];
