<?php

namespace Polirium\Core\Base\Extensions;

use Livewire\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    protected array $customComponents = [];

    public function setCustomComponents(array $components): void
    {
        foreach ($components as $component) {
            if (isset($component['alias']) && isset($component['class'])) {
                $this->customComponents[$component['alias']] = $component['class'];
            }
        }
    }

    public function resolveClassComponentClassName($name): ?string
    {
        // First check custom components (even with namespace)
        if (isset($this->customComponents[$name])) {
            return $this->customComponents[$name];
        }

        // Also check classComponents (for dynamically added components)
        if (isset($this->classComponents[$name])) {
            return $this->classComponents[$name];
        }

        // Fall back to parent logic
        return parent::resolveClassComponentClassName($name);
    }
}
