<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assignation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'assignations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tableau_id',
        'structure_id',
        'version',
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
            2 => 'Terminé',
            3 => 'Suspendu'
        ];

        return $statuts[$this->statut] ?? 'Inconnu';
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
        return $this->statut === 1;
    }

    /**
     * Vérifier si l'assignation est terminée
     */
    public function isCompleted()
    {
        return $this->statut === 2;
    }

    /**
     * Vérifier si l'assignation est suspendue
     */
    public function isSuspended()
    {
        return $this->statut === 3;
    }

    /**
     * Vérifier si l'assignation est en attente
     */
    public function isPending()
    {
        return $this->statut === 0;
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
}