<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Session;
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
        'created_by',
        'updated_by',
        'objectif_specifique_id',
        'hors_programme'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid();
                $model->created_by = Auth::user()->id;
            }
        });

        // Lors de la mise à jour
        static::updating(function ($model) {
            /* if ($model->created_by !== Auth::id()) {
                // Redirection avec message
                Session::flash('error', "Vous n'êtes pas autorisé à modifier cette activité.");
                throw new \Illuminate\Validation\ValidationException(
                    validator: \Illuminate\Support\Facades\Validator::make([], []),
                    response: redirect()->back()
                );
            } */
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            if ($model->created_by !== Auth::id()) {
                Session::flash('error', "Vous n'êtes pas autorisé à supprimer cette activité.");
                throw new \Illuminate\Validation\ValidationException(
                    validator: \Illuminate\Support\Facades\Validator::make([], []),
                    response: redirect()->back()
                );
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
        static::addGlobalScope('nonHorsProgramme', function ($builder) {
            $builder->where('hors_programme', false);
        });
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
    public function scopeWithHorsProgramme($query)
    {
        return $query->withoutGlobalScope('nonHorsProgramme');
    }

    public function scopeOnlyHorsProgramme($query)
    {
        return $query->withoutGlobalScope('nonHorsProgramme')
            ->where('hors_programme', true);
    }
}
