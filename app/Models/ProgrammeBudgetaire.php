<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
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
        return $this->hasManyDeep(
            Activite::class,
            [
                ObjectifsStrategique::class,
                Action::class,
                ObjectifSpecifique::class,
            ],
            // clés étrangères sur chaque modèle intermédiaire
            [
                'programme_budgetaire_id',   // objectifs_strategiques → programme_budgetaires
                'objectif_strategique_id',   // actions → objectifs_strategiques
                'action_id',                 // objectifs_specifiques → actions
                'objectif_specifique_id',    // activites → objectifs_specifiques
            ],
            // clés locales correspondantes
            [
                'id', // programme_budgetaires.id
                'id', // objectifs_strategiques.id
                'id', // actions.id
                'id', // objectifs_specifiques.id
            ]
        );
    }

}
