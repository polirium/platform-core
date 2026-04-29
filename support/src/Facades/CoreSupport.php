<?php

namespace Polirium\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Polirium\Core\Support\Support\CoreSupport
 */
class CoreSupport extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'polirium:support';
    }
}
