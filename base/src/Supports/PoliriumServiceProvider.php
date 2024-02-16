<?php

namespace Polirium\Core\Base\Supports;

use Illuminate\Support\ServiceProvider;
use Polirium\Core\Base\Traits\LoadAndPublishDataTrait;

class PoliriumServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;
}
