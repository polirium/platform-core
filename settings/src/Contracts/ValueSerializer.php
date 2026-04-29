<?php

namespace Polirium\Core\Settings\Contracts;

interface ValueSerializer
{
    public function serialize($value): string;

    public function unserialize(string $serialized): mixed;
}
