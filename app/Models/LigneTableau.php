<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class LigneTableau extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use CausesActivity;
    use HasUlids;

    protected $table = 'lignes_tableau';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        'nom_ligne',
        'ordre',
        'tableau_id',
        'parent_id'
    ];

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }

    public function parent()
    {
        return $this->belongsTo(LigneTableau::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(LigneTableau::class, 'parent_id');
    }

    public function cellules()
    {
        return $this->hasMany(Cellule::class, 'ligne_id');
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
            ->useLogName('Ligne Tableau')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
