<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Version extends Model
{
    use HasFactory, Notifiable;
    use LogsActivity;
    use HasUlids;
    use CausesActivity;
    use SoftDeletes;
    use HasRoles;


    protected $fillable = [
        'code',
        'libelle',
        'annee',
        'is_locked',
        'date_verrouillage',
        'locked_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'date_verrouillage' => 'datetime',
    ];

    public function assignations()
    {
        return $this->hasMany(Assignation::class);
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    /**
     * Calcul de l'état de consolidation de l'annuaire pour cette version
     */
    public function tauxValidation(): array
    {
        $total = $this->assignations()->count();
        $validees = $this->assignations()->where('statut', 3)->count();
        $transmises = $this->assignations()->where('statut', 2)->count();
        $enCours = $this->assignations()->where('statut', 1)->count();
        $rejetees = $this->assignations()->where('statut', 4)->count();
        $enAttente = $this->assignations()->where('statut', 0)->count();

        $pourcentage = $total > 0 ? round(($validees / $total) * 100) : 0;
        $pourcentageTransmis = $total > 0 ? round((($validees + $transmises) / $total) * 100) : 0;

        return [
            'total' => $total,
            'validees' => $validees,
            'transmises' => $transmises,
            'en_cours' => $enCours,
            'rejetees' => $rejetees,
            'en_attente' => $enAttente,
            'pourcentage' => $pourcentage,
            'pourcentage_transmis' => $pourcentageTransmis,
            'est_complet' => $total > 0 && $validees === $total,
            'est_verrouille' => $this->isLocked(),
        ];
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
            ->useLogName('Version')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
