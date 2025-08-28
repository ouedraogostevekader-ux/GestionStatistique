<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;
    use HasUlids, LogsActivity;
    protected $primaryKey = 'ulid';
    protected $keyType = 'string';

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created')
        { return 'Creation';}

        if ($eventName == 'updated')
        {return 'Modification';}

        if ($eventName == 'deleted')
        {return 'Suppression';}

        return "{$eventName}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Permission')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
