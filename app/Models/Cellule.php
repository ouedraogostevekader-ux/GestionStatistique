<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class Cellule extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use CausesActivity;
    use HasUlids;

    protected $table = 'cellules';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        'ligne_id',
        'colonne_id'
    ];

    public function ligne()
    {
        return $this->belongsTo(LigneTableau::class, 'ligne_id');
    }

    public function colonne()
    {
        return $this->belongsTo(ColonneTableau::class, 'colonne_id');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'cellule_id');
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
            ->useLogName('Cellule')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
