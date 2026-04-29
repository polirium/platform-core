<?php

namespace Polirium\Core\Support;

use Carbon\Carbon;
use Settings;

class Support
{
    public function date(Carbon|string $datetime): string
    {
        return Carbon::parse($datetime)->format(Settings::get('date_format', config('core.base.setting.date_format')));
    }

    public function time(Carbon|string $datetime): string
    {
        return Carbon::parse($datetime)->format(Settings::get('time_format', config('core.base.setting.time_format')));
    }

    public function datetime(Carbon|string $datetime): string
    {
        return Carbon::parse($datetime)->format(Settings::get('datetime_format', config('core.base.setting.datetime_format')));
    }
}
