<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class ProgrammeBudgetaire extends Model
{
    use HasFactory, SoftDeletes, HasUlids;
    //use HasRelationships;

    protected $table = 'programmes_budgetaires';

    protected $fillable = [
        'libelle',
        'numero_programme',
        'programme_activite_id',
    ];

    public function objectifsStrategiques()
    {
        return $this->hasMany(ObjectifsStrategique::class, 'programme_budgetaire_id', 'id');
    }
    public function programmeActivite()
    {
        return $this->belongsTo(ProgrammesActivite::class, 'programme_activite_id');
    }
    public function structures()
    {
        return $this->belongsToMany(Structure::class, 'programme_budgetaire_structure');
    }

    public function responsables()
    {
        return $this->belongsToMany(User::class, 'responsables')
            ->withPivot('date_debut', 'date_fin')
            ->withTimestamps();
    }

    public function activites()
    {
        return $this->hasMany(Activite::class, 'objectif_specifique_id')
            ->join('objectifs_specifiques', 'activites.objectif_specifique_id', '=', 'objectifs_specifiques.id')
            ->join('actions', 'objectifs_specifiques.action_id', '=', 'actions.id')
            ->join('objectifs_strategiques', 'actions.objectif_strategique_id', '=', 'objectifs_strategiques.id')
            ->where('objectifs_strategiques.programme_budgetaire_id', $this->id)
            ->select('activites.*');
    }

}
