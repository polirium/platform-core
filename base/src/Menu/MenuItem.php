<?php

namespace Polirium\Core\Base\Menu;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class MenuItem
{
    /** @var array<string, mixed> */
    public array $attributes;

    /** @var array<string, mixed> */
    private array $data = [];

    /** @var Collection<int, MenuItem>|null */
    private ?Collection $visibleChildren = null;

    public readonly ?string $parentId;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly MenuBuilder $builder,
        public readonly string $id,
        public readonly string $title,
        private readonly array $options,
    ) {
        $this->parentId = isset($options['parent']) ? (string) $options['parent'] : null;
        $this->attributes = array_diff_key($options, array_flip([
            'route',
            'url',
            'parent',
        ]));
    }

    /**
     * @param array<string, mixed>|string $options
     */
    public function add(string $title, array|string $options = ''): self
    {
        $options = is_array($options) ? $options : ['url' => $options];
        $options['parent'] = $this->id;

        return $this->builder->add($title, $options);
    }

    public function data(string $key, mixed $value = null): mixed
    {
        if (func_num_args() === 1) {
            return $this->data[$key] ?? null;
        }

        $this->data[$key] = $value;

        return $this;
    }

    /** @return Collection<int, MenuItem> */
    public function children(): Collection
    {
        return $this->visibleChildren ?? $this->builder->childrenOf($this->id);
    }

    /** @param Collection<int, MenuItem> $children */
    public function setVisibleChildren(Collection $children): void
    {
        $this->visibleChildren = $children->values();
    }

    public function hasChildren(): bool
    {
        return $this->children()->isNotEmpty();
    }

    public function url(): ?string
    {
        if (isset($this->options['route'])) {
            $route = $this->options['route'];

            if (is_array($route)) {
                return URL::route((string) array_shift($route), $route);
            }

            return URL::route((string) $route);
        }

        if (! isset($this->options['url'])) {
            return null;
        }

        $url = (string) $this->options['url'];

        return parse_url($url, PHP_URL_SCHEME) ? $url : URL::to($url);
    }

    public function matchesCurrentRequest(): bool
    {
        if (isset($this->options['route'])) {
            $route = $this->options['route'];
            $routeName = is_array($route) ? reset($route) : $route;

            return is_string($routeName) && request()->routeIs($routeName);
        }

        $url = $this->url();

        return $url !== null && rtrim(request()->url(), '/') === rtrim($url, '/');
    }

    public function markActive(): void
    {
        $classes = preg_split('/\s+/', trim((string) ($this->attributes['class'] ?? ''))) ?: [];
        $classes[] = 'active';
        $this->attributes['class'] = implode(' ', array_unique(array_filter($classes)));
    }
}
