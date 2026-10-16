<?php

namespace Polirium\Core\Base\Extensions;

use Livewire\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    protected array $customComponents = [];

    public function addComponent($name = null, $viewPath = null, $class = null): void
    {
        // Livewire normalizes aliases before resolving them, so explicit
        // registrations must use that same canonical name. Preserve the
        // parent's single-class-argument behavior by only normalizing when an
        // alias is paired with a class or view path.
        if ($name !== null && ($class !== null || $viewPath !== null)) {
            $name = $this->normalizeName($name);
        }

        parent::addComponent($name, $viewPath, $class);
    }

    public function setCustomComponents(array $components): void
    {
        foreach ($components as $component) {
            if (isset($component['alias']) && isset($component['class'])) {
                // Livewire 4 canonicalizes slash-style aliases to dot notation
                // before resolving them. Keep our registry in that same form so
                // aliases such as `core/ui::component` remain resolvable.
                $alias = $this->normalizeName($component['alias']);

                $this->customComponents[$alias] = $component['class'];
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
