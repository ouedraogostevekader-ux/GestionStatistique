<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

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
}
