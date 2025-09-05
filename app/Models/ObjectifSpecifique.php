<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectifSpecifique extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'objectifs_specifiques';

    protected $fillable = [
        'libelle',
        'action_id',
    ];

    // Relations
    public function action()
    {
        return $this->belongsTo(Action::class, 'action_id', 'id');
    }

    public function activites()
    {
        return $this->hasMany(Activite::class, 'objectif_specifique_id', 'id');
    }

    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'objectif_specifique_id', 'id');
    }
}
