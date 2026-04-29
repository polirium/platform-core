<?php

namespace Polirium\Core\Settings\Support\KeyGenerators;

use Illuminate\Support\Str;
use Polirium\Core\Settings\Contracts\ContextSerializer;
use Polirium\Core\Settings\Contracts\KeyGenerator;
use Polirium\Core\Settings\Support\Context;

class ReadableKeyGenerator implements KeyGenerator
{
    protected ContextSerializer $serializer;

    public function generate(string $key, Context $context = null): string
    {
        $key = $this->normalizeKey($key);

        if ($context) {
            $key .= $this->contextPrefix() . $this->serializer->serialize($context);
        }

        return $key;
    }

    public function removeContextFromKey(string $key): string
    {
        return Str::before($key, $this->contextPrefix());
    }

    public function setContextSerializer(ContextSerializer $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }

    public function contextPrefix(): string
    {
        return ':c:::';
    }

    protected function normalizeKey(string $key): string
    {
        return Str::of($key)
            ->replace('.', '-dot-')
            ->slug()
            ->replace('-dot-', '.')
            ->__toString();
    }
}
