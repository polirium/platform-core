<?php

namespace Polirium\Core\Settings\Support;

class SettingRegistry
{
    protected array $groups = [];
    protected ?string $currentGroup = null;

    /**
     * Start defining a settings group
     */
    public function group(string $name, array $config = []): self
    {
        $this->currentGroup = $name;

        if (! isset($this->groups[$name])) {
            $this->groups[$name] = [
                'title' => $config['title'] ?? ucfirst(str_replace('_', ' ', $name)),
                'icon' => $config['icon'] ?? 'settings',
                'description' => $config['description'] ?? null,
                'settings' => [],
            ];
        }

        return $this;
    }

    /**
     * Add a setting to the current group
     */
    public function add(string $key, array $config): self
    {
        if (! $this->currentGroup) {
            throw new \InvalidArgumentException('You must call group() before adding settings');
        }

        $this->groups[$this->currentGroup]['settings'][$key] = array_merge([
            'type' => 'text',
            'label' => ucfirst(str_replace('_', ' ', $key)),
            'description' => null,
            'default' => null,
            'required' => false,
            'validation' => [],
            'options' => [], // For select, radio, checkbox
            'attributes' => [], // HTML attributes
        ], $config);

        return $this;
    }

    /**
     * Get all registered groups
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Get a specific group
     */
    public function getGroup(string $name): ?array
    {
        return $this->groups[$name] ?? null;
    }

    /**
     * Get all settings for a group
     */
    public function getGroupSettings(string $group): array
    {
        return $this->groups[$group]['settings'] ?? [];
    }

    /**
     * Get a specific setting configuration
     */
    public function getSetting(string $group, string $key): ?array
    {
        return $this->groups[$group]['settings'][$key] ?? null;
    }

    /**
     * Check if a group exists
     */
    public function hasGroup(string $name): bool
    {
        return isset($this->groups[$name]);
    }

    /**
     * Check if a setting exists in a group
     */
    public function hasSetting(string $group, string $key): bool
    {
        return isset($this->groups[$group]['settings'][$key]);
    }

    /**
     * Remove a group
     */
    public function removeGroup(string $name): self
    {
        unset($this->groups[$name]);

        return $this;
    }

    /**
     * Remove a setting from a group
     */
    public function removeSetting(string $group, string $key): self
    {
        unset($this->groups[$group]['settings'][$key]);

        return $this;
    }

    /**
     * Clear all groups
     */
    public function clear(): self
    {
        $this->groups = [];
        $this->currentGroup = null;

        return $this;
    }
}
