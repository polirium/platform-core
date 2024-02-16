<?php

namespace Polirium\Core\Settings\Contracts;

use Polirium\Core\Settings\Support\Context;

interface ContextSerializer
{
    public function serialize(Context $context = null): string;
}
