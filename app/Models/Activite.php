<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Activite extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use HasRoles;
    protected $table = 'activites';
    protected $keyType = 'string'; // car ULID
    public $incrementing = false;

    protected $fillable = [
        'cout_previsionnel',
        'libelle',
        'objectif_specifique_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid();
            }
        });
    }

    /**
     * Relation avec ObjectifSpecifique
     */
    public function objectifSpecifique()
    {
        return $this->belongsTo(ObjectifSpecifique::class);
    }
    public function periodes()
    {
        return $this->belongsToMany(Periode::class, 'activite_periode')->withPivot('annee');
    }

    public function indicateurs()
    {
        return $this->hasMany(IndicateurProgramme::class, 'activites_id');
    }

    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'activites_id', 'id');
    }

    /* Vérifier si une activité est modifiable */
    public function activiteIsEdit()
    {
        // Vérifier si le programme budgétaire est actif
        return $this->objectifSpecifique->action->objectifStrategique->programmeBudgetaire->isActive;
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
            ->useLogName('Activité')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::deleting(function ($activite) {
            if ($activite->isForceDeleting()) {
                $activite->indicateursProgrammes()->withTrashed()->forceDelete();
                $activite->periodes()->detach();
            } else {
                $activite->indicateursProgrammes()->delete();
            }
        });

        static::restoring(function ($activite) {
            $activite->indicateursProgrammes()->withTrashed()->restore();
        });
    }
}
