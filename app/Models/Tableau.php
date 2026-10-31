<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Tableau extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use CausesActivity;
    use SoftDeletes;
    use HasRoles;
    use HasUlids;

    protected $table = 'tableaux';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'libelle',
        'description',
        'categorie_id'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function reglesDeGestion()
    {
        return $this->hasMany(RegleDeGestion::class, 'tableau_id');
    }

    public function assignations()
    {
        return $this->hasMany(Assignation::class, 'tableau_id');
    }

    public function lignesTableau()
    {
        return $this->hasMany(LigneTableau::class, 'tableau_id');
    }

    public function lignesRacines()
    {
        return $this->hasMany(LigneTableau::class, 'tableau_id')->whereNull('parent_id')->orderBy('ordre');
    }

    public function colonnesTableau()
    {
        return $this->hasMany(ColonneTableau::class, 'tableau_id');
    }

    public function colonnesRacines()
    {
        return $this->hasMany(ColonneTableau::class, 'tableau_id')->whereNull('parent_id')->orderBy('ordre');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'tableau_id');
    }

    public function cellules()
    {
        return $this->hasManyThrough(Cellule::class, LigneTableau::class);
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
            ->useLogName('Tableau')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
