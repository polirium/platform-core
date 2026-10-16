<?php

namespace Polirium\Core\Base\Menu;

use Illuminate\Support\Collection;

class MenuBuilder
{
    /** @var Collection<int, MenuItem> */
    private Collection $items;

    public function __construct(public readonly string $name)
    {
        $this->items = collect();
    }

    /**
     * @param array<string, mixed>|string $options
     */
    public function add(string $title, array|string $options = ''): MenuItem
    {
        $options = is_array($options) ? $options : ['url' => $options];
        $id = (string) ($options['id'] ?? str_replace('.', '', uniqid('menu-', true)));
        $item = new MenuItem($this, $id, $title, $options);
        $this->items->push($item);

        return $item;
    }

    public function find(string $id): ?MenuItem
    {
        return $this->items->first(fn (MenuItem $item): bool => $item->id === $id);
    }

    /** @return Collection<int, MenuItem> */
    public function roots(): Collection
    {
        return $this->childrenOf(null);
    }

    /** @return Collection<int, MenuItem> */
    public function childrenOf(?string $parentId): Collection
    {
        return $this->items
            ->filter(fn (MenuItem $item): bool => $item->parentId === $parentId)
            ->values();
    }

    public function markActiveItems(): void
    {
        foreach ($this->items as $item) {
            if (! $item->matchesCurrentRequest()) {
                continue;
            }

            $item->markActive();
            $parentId = $item->parentId;

            while ($parentId !== null && ($parent = $this->find($parentId)) !== null) {
                $parent->markActive();
                $parentId = $parent->parentId;
            }
        }
    }
}
