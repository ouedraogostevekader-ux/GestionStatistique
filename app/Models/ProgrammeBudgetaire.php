<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;



class ProgrammeBudgetaire extends Model
{
    use HasFactory, SoftDeletes, HasUlids;
    use HasRoles;
    use LogsActivity;
    //use HasRelationships;

    protected $table = 'programmes_budgetaires';

    protected $fillable = [
        'libelle',
        'numero_programme',
        'annee',
        'is_active',
        'programme_activite_id',
    ];
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
            ->useLogName('Programme Budgetaire')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function objectifsStrategiques()
    {
        return $this->hasMany(ObjectifsStrategique::class, 'programme_budgetaire_id', 'id');
    }
    public function programmeActivite()
    {
        return $this->belongsTo(ProgrammesActivite::class, 'programme_activite_id');
    }
    public function structures()
    {
        return $this->belongsToMany(Structure::class, 'programme_budgetaire_structure', 'programme_budgetaire_id', 'structure_id');
    }

    public function responsables()
    {
        return $this->belongsToMany(User::class, 'responsables')->withPivot('date_debut', 'date_fin');
    }



    public function activites()
    {
        return $this->hasMany(Activite::class, 'objectif_specifique_id')
            ->join('objectifs_specifiques', 'activites.objectif_specifique_id', '=', 'objectifs_specifiques.id')
            ->join('actions', 'objectifs_specifiques.action_id', '=', 'actions.id')
            ->join('objectifs_strategiques', 'actions.objectif_strategique_id', '=', 'objectifs_strategiques.id')
            ->where('objectifs_strategiques.programme_budgetaire_id', $this->id)
            ->select('activites.*');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($programme) {
            // Supprimer relations pivot car cascadesondelete n'a pas ete fait sur les migrations 
            $programme->structures()->detach();
            $programme->responsables()->detach();
            if ($programme->isForceDeleting()) {
                // Suppression définitive
                $programme->objectifsStrategiques()->withTrashed()->forceDelete();

            } else {
                // Suppression logique
                $programme->objectifsStrategiques()->delete();
            }

        });
    }

}
