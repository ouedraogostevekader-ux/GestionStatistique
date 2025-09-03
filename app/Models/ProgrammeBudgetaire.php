<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class ProgrammeBudgetaire extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

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
    //      public function programmeActivite()
    // {
    //     return $this->belongsTo(ProgrammeActivite::class, 'programme_activite_id');
    // }

}
