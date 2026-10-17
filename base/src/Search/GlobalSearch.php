<?php

namespace Polirium\Core\Base\Search;

use Closure;
use Throwable;

/**
 * Sources for the header search (Ctrl+K).
 *
 * Each module registers its own sources while it boots, so a disabled module
 * contributes nothing. A source declares the permission needed to see its
 * results and a callback returning at most $limit hits for a term:
 *
 *     app(GlobalSearch::class)->register('customers', [
 *         'label' => 'modules/customer::customer.customers',
 *         'icon' => 'users',
 *         'permission' => 'customers.index',
 *         'search' => fn (string $term, int $limit) => [...],
 *     ]);
 *
 * Each hit is ['title' => ..., 'subtitle' => ..., 'url' => ...]. Callbacks
 * must apply the user's branch themselves (see user_branch()).
 */
class GlobalSearch
{
    /** @var array<string, array{label: string, icon: string, permission: string|array|null, order: int, search: Closure}> */
    protected array $sources = [];

    public function register(string $key, array $source): static
    {
        $this->sources[$key] = [
            'label' => (string) ($source['label'] ?? $key),
            'icon' => (string) ($source['icon'] ?? 'search'),
            'permission' => $source['permission'] ?? null,
            'order' => (int) ($source['order'] ?? 100),
            'search' => $source['search'],
        ];

        return $this;
    }

    /**
     * Result groups the user may see, in source order. A failing source is
     * reported and skipped so it never breaks the search box.
     *
     * @return list<array{key: string, label: string, icon: string, items: list<array{title: string, subtitle: ?string, url: string}>}>
     */
    public function search(string $term, $user = null, int $limit = 5): array
    {
        $user ??= auth()->user();
        $term = trim($term);

        if (! $user || mb_strlen($term) < 2) {
            return [];
        }

        return collect($this->sources)
            ->sortBy('order')
            ->filter(fn ($source) => $this->allows($user, $source['permission']))
            ->map(function ($source, $key) use ($term, $limit) {
                try {
                    $items = collect(($source['search'])($term, $limit))
                        ->take($limit)
                        ->map(fn ($item) => [
                            'title' => (string) ($item['title'] ?? ''),
                            'subtitle' => isset($item['subtitle']) ? (string) $item['subtitle'] : null,
                            'url' => (string) ($item['url'] ?? '#'),
                        ])
                        ->values()
                        ->all();
                } catch (Throwable $exception) {
                    report($exception);
                    $items = [];
                }

                return ['key' => $key, 'label' => trans($source['label']), 'icon' => $source['icon'], 'items' => $items];
            })
            ->filter(fn ($group) => $group['items'] !== [])
            ->values()
            ->all();
    }

    /**
     * Escape LIKE wildcards in a user-typed term.
     */
    public static function like(string $term): string
    {
        return addcslashes($term, '\\%_');
    }

    protected function allows($user, string|array|null $permission): bool
    {
        if (empty($permission) || ($user->super_admin ?? false)) {
            return true;
        }

        foreach ((array) $permission as $name) {
            if ($user->can($name)) {
                return true;
            }
        }

        return false;
    }
}
