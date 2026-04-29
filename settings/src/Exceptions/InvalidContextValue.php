<?php

namespace Polirium\Core\Settings\Exceptions;

use Exception;

final class InvalidContextValue extends Exception
{
    public static function forKey(string $key): self
    {
        return new self("An invalid context value was provided for key: {$key}. Only string, integer, and boolean values are allowed.");
    }
}
