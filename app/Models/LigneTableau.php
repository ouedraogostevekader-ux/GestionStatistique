<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LigneTableau extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lignes_tableau';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nom_ligne',
        'ordre',
        'tableau_id',
        'parent_id'
    ];

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }

    public function parent()
    {
        return $this->belongsTo(LigneTableau::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(LigneTableau::class, 'parent_id');
    }

    public function cellules()
    {
        return $this->hasMany(Cellule::class, 'ligne_id');
    }
}