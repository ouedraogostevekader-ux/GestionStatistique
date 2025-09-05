<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Action extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    use LogsActivity;
    use HasRoles;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'libelle',
        'objectif_strategique_id',
    ];

    // Relations
    public function objectifStrategique()
    {
        return $this->belongsTo(ObjectifsStrategique::class, 'objectif_strategique_id', 'id');
    }


    public function objectifsSpecifiques()
    {
        return $this->hasMany(ObjectifSpecifique::class, 'action_id', 'id');
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
            ->useLogName('Action')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
