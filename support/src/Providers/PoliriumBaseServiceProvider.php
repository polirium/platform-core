<?php

namespace Polirium\Core\Support\Providers;

use Illuminate\Support\ServiceProvider;
use Polirium\Core\Support\Traits\LoadAndPublishDataTrait;

class PoliriumBaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

}
