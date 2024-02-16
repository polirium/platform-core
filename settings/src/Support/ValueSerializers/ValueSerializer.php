<?php

namespace Polirium\Core\Settings\Support\ValueSerializers;

use Illuminate\Support\Arr;
use Polirium\Core\Settings\Contracts\ValueSerializer as ValueSerializerContract;

class ValueSerializer implements ValueSerializerContract
{
    public function serialize($value): string
    {
        return serialize($value);
    }

    public function unserialize(string $serialized): mixed
    {
        $safelistedClasses = Arr::wrap(config('settings.unserialize_safelist', []));

        return unserialize($serialized, ['allowed_classes' => $safelistedClasses]);
    }
}
