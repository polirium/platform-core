<?php

return [
    'table' => 'settings',
    'cache' => true,
    'cache_key_prefix' => 'settings.',
    'encryption' => true,
    'driver' => env('SETTINGS_DRIVER', 'eloquent'),
    'drivers' => [
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
        'eloquent' => [
            'driver' => 'eloquent',
            'model' => \Polirium\Core\Settings\Http\Models\Setting::class,
        ],
    ],
    'teams' => true,
    'context_serializer' => \Polirium\Core\Settings\Support\ContextSerializers\ContextSerializer::class,
    'key_generator' => \Polirium\Core\Settings\Support\KeyGenerators\Md5KeyGenerator::class,
    'value_serializer' => \Polirium\Core\Settings\Support\ValueSerializers\ValueSerializer::class,
    'cache_default_value' => true,
    'unserialize_safelist' => [
        \Carbon\Carbon::class,
        \Carbon\CarbonImmutable::class,
        \Illuminate\Support\Carbon::class,
    ],
];
