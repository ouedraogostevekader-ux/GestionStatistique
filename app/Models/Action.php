<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'libelle',
        'objectif_strategique_id',
    ];

    // Relations
    // public function objectifStrategique()
    // {
    //     return $this->belongsTo(ObjectifStrategique::class);
    // }

    // public function objectifsSpecifiques()
    // {
    //     return $this->hasMany(ObjectifSpecifique::class);
    // }
}
