<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class ObjectifSpecifique extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    use LogsActivity;
    use HasRoles;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'objectifs_specifiques';

    protected $fillable = [
        'libelle',
        'annee',
        'action_id',
    ];

    // Relations
    public function action()
    {
        return $this->belongsTo(Action::class, 'action_id', 'id');
    }

    public function activites()
    {
        return $this->hasMany(Activite::class, 'objectif_specifique_id', 'id');
    }

    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'objectif_specifique_id', 'id');
    }
    public function indicateurs()
    {
        return $this->hasMany(IndicateurProgramme::class, 'objectif_specifique_id', 'id');
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
            ->useLogName('Objectifs Spécifiques')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::deleting(function ($objectifSpecifique) {
            if ($objectifSpecifique->isForceDeleting()) {
                $objectifSpecifique->activites()->withTrashed()->forceDelete();
                $objectifSpecifique->indicateursProgrammes()->withTrashed()->forceDelete();
            } else {
                $objectifSpecifique->activites()->delete();
                $objectifSpecifique->indicateursProgrammes()->delete();
            }
        });

        static::restoring(function ($objectifSpecifique) {
            $objectifSpecifique->activites()->withTrashed()->restore();
            $objectifSpecifique->indicateursProgrammes()->withTrashed()->restore();
        });
    }
}
