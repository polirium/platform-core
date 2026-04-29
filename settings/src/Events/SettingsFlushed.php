<?php

namespace Polirium\Core\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Polirium\Core\Settings\Support\Context;

final class SettingsFlushed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public bool|Collection|string $keys,
        public mixed $teamId,
        public bool|Context|null $context,
    ) {
    }
}
