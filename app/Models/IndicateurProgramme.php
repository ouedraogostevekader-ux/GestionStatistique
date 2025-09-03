<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicateurProgramme extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'indicateurs_programmes';

    protected $fillable = [
        'libelle',
        'unite_mesure',
        'valeur_ref',
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
}
