<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectifSpecifique extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'objectifs_specifiques';

    protected $fillable = [
        'libelle',
        'action_id',
    ];

    // Relations
    // public function action()
    // {
    //     return $this->belongsTo(Action::class);
    // }

    // public function activites()
    // {
    //     return $this->hasMany(Activite::class);
    // }

    // public function indicateursProgrammes()
    // {
    //     return $this->hasMany(IndicateurProgramme::class);
    // }
}
