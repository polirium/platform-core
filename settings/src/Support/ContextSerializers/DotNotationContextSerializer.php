<?php

namespace Polirium\Core\Settings\Support\ContextSerializers;

use Polirium\Core\Settings\Contracts\ContextSerializer as ContextSerializerContract;
use Polirium\Core\Settings\Support\Context;

class DotNotationContextSerializer implements ContextSerializerContract
{
    public function serialize(Context $context = null): string
    {
        if (is_null($context)) {
            return '';
        }

        return collect($context->toArray())
            ->map(function ($value, string $key) {
                $value = match ($key) {
                    'model' => rescue(fn () => app($value)->getMorphClass(), $value),
                    default => $value,
                };

                if ($value === false) {
                    $value = 0;
                }

                return "{$key}:{$value}";
            })
            ->implode('::');
    }
}
