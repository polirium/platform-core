<?php

namespace Polirium\Core\Base\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Polirium\Core\Base\Http\Models\Traits\HasUuid;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use LogsActivity;
    use HasUuid;

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = new LogOptions();
        $logOptions->logAll();
        $logOptions->logOnlyDirty();

        return $logOptions;
    }
}
