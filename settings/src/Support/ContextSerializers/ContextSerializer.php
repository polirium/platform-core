<?php

namespace Polirium\Core\Settings\Support\ContextSerializers;

use Polirium\Core\Settings\Contracts\ContextSerializer as ContextSerializerContract;
use Polirium\Core\Settings\Support\Context;

class ContextSerializer implements ContextSerializerContract
{
    public function serialize(Context $context = null): string
    {
        return serialize($context);
    }
}
