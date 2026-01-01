<?php

namespace Polirium\Core\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class RenderingAdminBarNotification
{
    use Dispatchable;

    public Collection $notifications;

    public function __construct()
    {
        $this->notifications = collect();
    }

    /**
     * Add a notification to the admin bar.
     *
     * @param array $notification ['title', 'description', 'actionUrl', 'isNew' => bool, 'dotColor' => 'red|green|blue|yellow']
     * @return $this
     */
    public function addNotification(array $notification): self
    {
        $this->notifications->push(array_merge([
            'title' => '',
            'description' => '',
            'actionUrl' => '#',
            'isNew' => false,
            'dotColor' => null,
        ], $notification));

        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function hasNotifications(): bool
    {
        return $this->notifications->isNotEmpty();
    }
}
