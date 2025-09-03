<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Suivi extends Model
{
    //
    use HasFactory, SoftDeletes;
    use HasUlids, LogsActivity;

    protected $fillable = [
        'cout_effectif',
        'resultat_atteint',
        'observations',
        'periode',
        'indicateur_programme_id',
    ];

    protected $casts = [
        //'cout_effectif' => 'decimal:15,2',
        //'resultat_atteint' => 'float',
    ];

    /**
     * Get the indicateur programme that owns the suivi.
     */
    public function indicateurProgramme()
    {
        return $this->belongsTo(IndicateurProgramme::class, 'indicateur_programme_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created')
        { return 'Creation';}

        if ($eventName == 'updated')
        {return 'Modification';}

        if ($eventName == 'deleted')
        {return 'Suppression';}

        return "{$eventName}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Suivi')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
