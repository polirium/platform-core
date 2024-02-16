<?php

namespace Polirium\Core\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Polirium\Core\Settings\Support\Context;

final class SettingWasDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $key,
        public string $storageKey,
        public string $cacheKey,
        public mixed $teamId,
        public bool|Context|null $context,
    ) {
    }
}
