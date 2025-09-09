<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'assignation_id');
    }
}