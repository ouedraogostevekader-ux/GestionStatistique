<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class ObjectifsStrategique extends Model
{
    use HasFactory;
    use HasUlids, LogsActivity, SoftDeletes;
    use LogsActivity;
    use HasRoles;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'libelle',
        'annee',
        'programme_budgetaire_id',
    ];

    public function programmeBudgetaire()
    {
        return $this->belongsTo(ProgrammeBudgetaire::class, 'programme_budgetaire_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'objectif_strategique_id', 'id');
    }

    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'objectif_strategique_id', 'id');
    }

    public function indicateurs()
    {
        return $this->hasMany(IndicateurProgramme::class, 'objectif_strategique_id', 'id');
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
            ->useLogName('Objectif Strategique')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected static function booted()
    {
        static::deleting(function ($objectifStrategique) {
            // Suppression logique en cascade pour les relations
            if ($objectifStrategique->isForceDeleting()) {
                // Suppression définitive
                $objectifStrategique->actions()->withTrashed()->forceDelete();
                $objectifStrategique->indicateursProgrammes()->withTrashed()->forceDelete();
            } else {
                // Suppression logique
                $objectifStrategique->actions()->delete();
                $objectifStrategique->indicateursProgrammes()->delete();
            }
        });

        static::restoring(function ($objectifStrategique) {
            // Restauration en cascade
            $objectifStrategique->actions()->withTrashed()->restore();
            $objectifStrategique->indicateursProgrammes()->withTrashed()->restore();
        });
    }
}
