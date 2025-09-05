<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Periode extends Model
{
    use HasFactory, SoftDeletes, HasUlids;
    use HasRoles;
    use LogsActivity;

    protected $table = 'periodes';
    protected $keyType = 'string'; // car ULID

    protected $fillable = ['libelle'];

    public function activites()
    {
        return $this->belongsToMany(Activite::class, 'activite_periode')->withPivot('annee')->withTimestamps();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return 'Creation';
        }

        if ($eventName == 'updated') {
            return 'Modification';
        }

        if ($eventName == 'deleted') {
            return 'Suppression';
        }

        return "{$eventName}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Période')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
