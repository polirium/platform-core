<?php

namespace Polirium\Core\Base\Http\Livewire;

use Livewire\Component;
use Polirium\Core\Base\Menu\MenuManager;
use Polirium\Core\Base\Search\GlobalSearch;
use Polirium\Core\UI\View\Components\Header\Menu;
use Throwable;

/**
 * Header search (Ctrl+K): menu entries the user may open, filtered in the
 * browser, plus records from the sources modules register in GlobalSearch.
 */
class GlobalSearchComponent extends Component
{
    public string $query = '';

    /**
     * Menu entries, built on the first render only (the browser keeps them).
     */
    protected array $menuItems = [];

    public function mount(): void
    {
        $this->menuItems = $this->buildMenuItems();
    }

    /**
     * Leaf menu entries the user may open, with their parents as a path.
     *
     * @return list<array{title: string, path: string, url: string}>
     */
    protected function buildMenuItems(): array
    {
        try {
            $menu = app(MenuManager::class)->get('polirium.core.menu');

            if (! $menu) {
                return [];
            }

            $items = [];
            $walk = function ($nodes, array $path) use (&$walk, &$items): void {
                foreach ($nodes as $node) {
                    $title = trim(strip_tags((string) trans($node->title)));

                    if ($node->hasChildren()) {
                        $walk($node->children(), [...$path, $title]);

                        continue;
                    }

                    $url = rescue(fn () => $node->url(), null, false);

                    if ($url && $url !== '#') {
                        $items[] = ['title' => $title, 'path' => implode(' › ', $path), 'url' => $url];
                    }
                }
            };

            $walk(app(Menu::class)->filterMenuItemsByPermission($menu->roots()), []);

            return $items;
        } catch (Throwable $exception) {
            report($exception);

            return [];
        }
    }

    public function render()
    {
        return view('core/base::global-search', [
            'menuItems' => $this->menuItems,
            'groups' => mb_strlen(trim($this->query)) >= 2
                ? app(GlobalSearch::class)->search(mb_substr($this->query, 0, 100))
                : [],
        ]);
    }
}
