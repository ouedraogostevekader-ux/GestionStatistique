<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'libelle',
        'objectif_strategique_id',
    ];

    // Relations
    public function objectifStrategique()
    {
        return $this->belongsTo(ObjectifsStrategique::class, 'objectif_strategique_id', 'id');
    }


    public function objectifsSpecifiques()
    {
        return $this->hasMany(ObjectifSpecifique::class, 'action_id', 'id');
    }
}
