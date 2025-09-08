<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Validation\ValidationException;
class IndicateurProgramme extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    use LogsActivity;
    use HasRoles;
    protected $table = 'indicateurs_programmes';

    protected $fillable = [
        'libelle',
        'unite_mesure',
        'valeur_ref',
        'annee',
        'valeur_cible',
        'objectif_specifique_id',
        'objectif_strategique_id',
        'activites_id',
    ];

    // Relations
    public function objectifSpecifique()
    {
        return $this->belongsTo(ObjectifSpecifique::class);
    }

    public function objectifStrategique()
    {
        return $this->belongsTo(ObjectifsStrategique::class);
    }

    public function activite()
    {
        return $this->belongsTo(Activite::class, 'activites_id');
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class);
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
            ->useLogName('Indicateur Programme')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::saving(function ($indicateur) {
            // Liste des clés à vérifier
            $keys = [
                $indicateur->objectif_specifique_id,
                $indicateur->objectif_strategique_id,
                $indicateur->activites_id,
            ];

            // Compter combien de clés sont NON NULL
            $nonNullCount = collect($keys)->filter(fn($v) => !is_null($v))->count();

            if ($nonNullCount === 0) {
                // Aucun foreign key fourni
                Session::flash('error', "Vous devez renseigner exactement un lien (objectif spécifique, objectif stratégique ou activité).");

                throw ValidationException::withMessages([
                    'foreign_keys' => "Un seul et un seul foreign key doit être renseigné.",
                ]);
            }

            if ($nonNullCount > 1) {
                // Plusieurs foreign keys remplis
                Session::flash('error', "Vous ne pouvez lier l'indicateur qu'à un seul élément à la fois.");

                throw ValidationException::withMessages([
                    'foreign_keys' => "Un seul et un seul foreign key doit être renseigné.",
                ]);
            }
        });
        static::deleting(function ($indicateur) {
            if ($indicateur->isForceDeleting()) {
                $indicateur->suivis()->withTrashed()->forceDelete();
            } else {
                $indicateur->suivis()->delete();
            }
        });

        static::restoring(function ($indicateur) {
            $indicateur->suivis()->withTrashed()->restore();
        });
    }
}
