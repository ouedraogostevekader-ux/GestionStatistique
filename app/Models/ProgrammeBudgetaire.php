<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammeBudgetaire extends Model
{
     use HasFactory, SoftDeletes,HasUlids;


    protected $table = 'programmes_budgetaires';

    protected $fillable = [
        'libelle',
        
        'numero_programme',
        'programme_activite_id',
        
        
    ];

    //      public function programmeActivite()
    // {
    //     return $this->belongsTo(ProgrammeActivite::class, 'programme_activite_id');
    // }

    public function structures()
    {
        return $this->belongsToMany(Structure::class, 'programme_budgetaire_structure', 'programme_budgetaire_id', 'structure_id');
    }
}
