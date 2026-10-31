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
        'motif_rejet',
        'date_validation',
        'valide_par',
        'date_rejet',
        'rejete_par',
        'derniere_relance_at',
        'relances_count',
        'responsable',
        'date_debut',
        'date_fin',
        'annee',
        'periode'
    ];

    protected $dates = [
        'date_debut',
        'annee',
        'date_validation',
        'date_rejet',
        'derniere_relance_at'
    ];

    protected $casts = [
        'statut' => 'integer',
        'date_validation' => 'datetime',
        'date_rejet' => 'datetime',
        'derniere_relance_at' => 'datetime',
        'relances_count' => 'integer',
    ];

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function rejetePar()
    {
        return $this->belongsTo(User::class, 'rejete_par');
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
            3 => 'Validé / Terminé',
            4 => 'Rejeté / À corriger'
        ];

        return $statuts[$this->statut] ?? 'Inconnu';
    }


    public static function determinerStatut($dateDebut, $dateFin)
    {
        $now = Carbon::today();

        $dateDebut = $dateDebut ? Carbon::parse($dateDebut) : null;
        $dateFin = $dateFin ? Carbon::parse($dateFin) : null;

        if (!$dateDebut) {
            return 0; // En attente
        }

        if ($dateDebut->isFuture() || $dateDebut->greaterThan($now)) {
            return 0; // En attente
        }

        return 1; // En cours
    }


    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatutClassAttribute()
    {
        $classes = [
            0 => 'secondary',
            1 => 'info',
            2 => 'warning',
            3 => 'success',
            4 => 'danger'
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
     * Vérifier si l'assignation est terminée / validée
     */
    public function isCompleted()
    {
        return (int) $this->statut === 3;
    }

    /**
     * Vérifier si l'assignation est validée par la DGESS
     */
    public function isValidated()
    {
        return (int) $this->statut === 3;
    }

    /**
     * Vérifier si l'assignation est rejetée par la DGESS
     */
    public function isRejected()
    {
        return (int) $this->statut === 4;
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
        return false;
    }

    /**
     * Vérifier si l'assignation est en attente
     */
    public function isPending()
    {
        return (int) $this->statut === 0;
    }

    /**
     * Vérifier si l'assignation est en retard
     */
    public function isEnRetard(): bool
    {
        if ((int) $this->statut === 3) {
            return false;
        }

        if (empty($this->date_fin)) {
            return false;
        }

        try {
            return Carbon::parse($this->date_fin)->endOfDay()->isPast();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Vérifier si l'échéance de l'assignation approche
     */
    public function isEcheanceProche(int $jours = 3): bool
    {
        if ((int) $this->statut === 3 || $this->isEnRetard()) {
            return false;
        }

        if (empty($this->date_fin)) {
            return false;
        }

        try {
            $dateFin = Carbon::parse($this->date_fin)->endOfDay();
            $now = Carbon::now();
            return $dateFin->isFuture() && $now->diffInDays($dateFin, false) <= $jours;
        } catch (\Exception $e) {
            return false;
        }
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
     * Scope pour les assignations transmises
     */
    public function scopeTransmises($query)
    {
        return $query->where('statut', 2);
    }

    /**
     * Scope pour les assignations validées
     */
    public function scopeValidees($query)
    {
        return $query->where('statut', 3);
    }

    /**
     * Scope pour les assignations rejetées
     */
    public function scopeRejetees($query)
    {
        return $query->where('statut', 4);
    }

    /**
     * Scope pour les assignations en retard
     */
    public function scopeRetardataires($query)
    {
        return $query->where('statut', '!=', 3)
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', Carbon::today()->toDateString());
    }

    /**
     * Scope pour les assignations dont l'échéance est proche
     */
    public function scopeEcheanceProche($query, $jours = 3)
    {
        $today = Carbon::today()->toDateString();
        $limite = Carbon::today()->addDays($jours)->toDateString();

        return $query->where('statut', '!=', 3)
            ->whereNotNull('date_fin')
            ->where('date_fin', '>=', $today)
            ->where('date_fin', '<=', $limite);
    }

    /**
     * Scope pour les assignations terminées (rétrocompatibilité)
     */
    public function scopeCompleted($query)
    {
        return $query->where('statut', 3);
    }

    /**
     * Obtenir le progrès de l'assignation
     */
    public function getProgressAttribute()
    {
        switch ((int) $this->statut) {
            case 0:
                return 0;   // En attente
            case 1:
                return 50;  // En cours
            case 2:
                return 85;  // Transmis / En attente de validation
            case 3:
                return 100; // Validé / Terminé
            case 4:
                return 30;  // Rejeté / À corriger
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
