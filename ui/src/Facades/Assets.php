<?php

namespace Polirium\Core\UI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|string getOption(string $key, array|string|null $default = null)
 * @method static string get(string|null $path)
 * @method static void add(string $type, string $path)
 * @method static \Illuminate\Support\HtmlString renderCss()
 * @method static \Illuminate\Support\HtmlString renderJs()
 *
 * @see \Polirium\Core\UI\Support\Assets
 */
class Assets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'polirium:assets';
    }
}
