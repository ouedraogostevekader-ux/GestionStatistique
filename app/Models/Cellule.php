<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cellule extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cellules';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ligne_id',
        'colonne_id'
    ];

    public function ligne()
    {
        return $this->belongsTo(LigneTableau::class, 'ligne_id');
    }

    public function colonne()
    {
        return $this->belongsTo(ColonneTableau::class, 'colonne_id');
    }

    public function soumissionsTableau()
    {
        return $this->hasMany(SoumissionTableau::class, 'cellule_id');
    }
}