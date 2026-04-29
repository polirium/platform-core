<?php

namespace Polirium\Core\Base\Http\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateLastLoginTimestamp
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (method_exists($event->user, 'updateLastLogin')) {
            $event->user->updateLastLogin();
        }
    }
}
