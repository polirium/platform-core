<?php

namespace Polirium\Core\Settings\Contracts;

use Polirium\Core\Settings\Support\Context;

interface KeyGenerator
{
    public function generate(string $key, Context $context = null): string;

    public function removeContextFromKey(string $key): string;

    public function setContextSerializer(ContextSerializer $serializer): self;

    public function contextPrefix(): string;
}
