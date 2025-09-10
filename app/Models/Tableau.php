<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;   


class Tableau extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tableaux';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
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

    public function colonnesTableau()
    {
        return $this->hasMany(ColonneTableau::class, 'tableau_id');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'tableau_id');
    }

    public function cellules()
    {
        return $this->hasManyThrough(Cellule::class, LigneTableau::class);
    }
}
