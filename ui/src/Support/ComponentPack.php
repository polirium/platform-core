<?php

namespace Polirium\Core\UI\Support;

use Polirium\Core\UI\Exceptions\ComponentAttributeException;
use Polirium\Core\UI\Support\UIConfig as Config;

abstract class ComponentPack
{
    private function checkAttribute(mixed $attribute): void
    {
        throw_if(! in_array($attribute, $this->keys()), new ComponentAttributeException($this));
    }

    private function getDefault(): mixed
    {
        $this->checkAttribute($this->default());

        return $this->get($this->default());
    }

    public function get(mixed $attribute = null): mixed
    {
        if (is_null($attribute) || $attribute === Config::GLOBAL) {
            return $this->getDefault();
        }

        return data_get($this->all(), $attribute) ?? $attribute;
    }

    public function mergeIf(bool $check, string $merge, mixed $attribute = null): mixed
    {
        if (! $check) {
            return $this->get($attribute);
        }

        $this->checkAttribute($merge);

        return collect($this->get($merge))->mergeRecursive($this->get($attribute))->toArray();
    }

    public function keys(): array
    {
        return array_keys($this->all());
    }

    abstract protected function default(): string;

    abstract public function all(): array;
}
