<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class Assignation extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use CausesActivity;
    use HasUlids;

    protected $table = 'assignations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        'tableau_id',
        'structure_id',
        'version_id',
        'source',
        'statut',
        'responsable',
        'date_debut',
        'date_fin',
        'annee',
        'periode'
    ];

    protected $dates = [
        'date_debut',
        'annee'
    ];

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid();
            }
        });
    }

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }

    /**
     * Relation avec la structure
     */
    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'assignation_id');
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatutLibelleAttribute()
    {
        $statuts = [
            0 => 'En attente',
            1 => 'En cours',
            2 => 'Transmis / En attente de validation',
            3 => 'Suspendu'
        ];

        return $statuts[$this->statut] ?? 'Inconnu';
    }


    public static function determinerStatut($dateDebut, $dateFin)
    {
        $now = Carbon::today(); // on compare uniquement la date sans l’heure

        // ✅ Vérification cohérente des dates
        $dateDebut = $dateDebut ? Carbon::parse($dateDebut) : null;
        $dateFin = $dateFin ? Carbon::parse($dateFin) : null;

        /*
        * Codes statut :
        * 0 = En attente (pas encore commencée)
        * 1 = En cours
        * 3 = Expirée
        */

        // 🔹 Cas 1 : Pas de date de début définie
        if (!$dateDebut) {
            return 0; // Considéré comme "en attente"
        }

        // 🔹 Cas 2 : Si la date de début est dans le futur par rapport à maintenant
        if ($dateDebut->isFuture() || $dateDebut->greaterThan($now)) {
            return 0; // "Assignation en attente"
        }

        // 🔹 Cas 3 : Si la date de fin est définie et déjà passée
        if ($dateFin && $now->greaterThan($dateFin)) {
            return 3; // "Assignation expirée"
        }

        // 🔹 Cas 4 : Si la date actuelle est comprise dans l’intervalle
        if ($dateDebut->lessThanOrEqualTo($now) && (!$dateFin || $now->lessThanOrEqualTo($dateFin))) {
            return 1; // "En cours"
        }

        // 🔹 Cas par défaut (sécurité)
        return 0;
    }


    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatutClassAttribute()
    {
        $classes = [
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'danger'
        ];

        return $classes[$this->statut] ?? 'secondary';
    }

    /**
     * Obtenir le libellé de la période
     */
    public function getPeriodeLibelleAttribute()
    {
        $periodes = [
            'T1' => 'Premier trimestre',
            'T2' => 'Deuxième trimestre',
            'T3' => 'Troisième trimestre',
            'T4' => 'Quatrième trimestre',
            'S1' => 'Premier semestre',
            'S2' => 'Deuxième semestre',
            'ANNUEL' => 'Annuel'
        ];

        return $periodes[$this->periode] ?? $this->periode;
    }

    /**
     * Vérifier si l'assignation est active
     */
    public function isActive()
    {
        return (int) $this->statut === 1;
    }

    /**
     * Vérifier si l'assignation est terminée
     */
    public function isCompleted()
    {
        return (int) $this->statut === 2;
    }

    /**
     * Vérifier si l'assignation est transmise / en attente de validation
     */
    public function isSubmitted()
    {
        return (int) $this->statut === 2;
    }

    /**
     * Vérifier si l'assignation est suspendue
     */
    public function isSuspended()
    {
        return (int) $this->statut === 3;
    }

    /**
     * Vérifier si l'assignation est en attente
     */
    public function isPending()
    {
        return (int) $this->statut === 0;
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    /**
     * Scope pour filtrer par année
     */
    public function scopeByAnnee($query, $annee)
    {
        return $query->where('annee', $annee);
    }

    /**
     * Scope pour les assignations actives
     */
    public function scopeActive($query)
    {
        return $query->where('statut', 1);
    }

    /**
     * Scope pour les assignations terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('statut', 2);
    }

    /**
     * Obtenir le progrès de l'assignation (à personnaliser selon vos besoins)
     */
    public function getProgressAttribute()
    {
        // Cette méthode peut être personnalisée pour calculer le progrès
        // basé sur les données saisies ou d'autres critères
        switch ($this->statut) {
            case 0:
                return 0;   // En attente
            case 1:
                return 50;  // En cours
            case 2:
                return 100; // Terminé
            case 3:
                return 25;  // Suspendu
            default:
                return 0;
        }
    }

    public function getPreviousAssignation()
    {
        return Assignation::where('structure_id', $this->structure_id)
            ->where('tableau_id', $this->tableau_id)
            ->whereHas('version', function ($q) {
                $q->where('annee', '<', $this->version->annee);
            })
            ->orderByDesc(
                Version::select('annee')
                    ->whereColumn('versions.id', 'assignations.version_id')
            )
            ->first();
    }


    //Methode pour les logs
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
            ->useLogName('Assignation')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
