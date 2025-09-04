<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectifsStrategique extends Model
{
    use HasFactory;
    use HasUlids, LogsActivity, SoftDeletes;
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
}
