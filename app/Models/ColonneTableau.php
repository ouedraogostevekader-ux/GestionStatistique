<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class ColonneTableau extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'colonnes_tableau';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'colonne_ligne',
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
        return $this->belongsTo(ColonneTableau::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(ColonneTableau::class, 'parent_id');
    }

    public function cellules()
    {
        return $this->hasMany(Cellule::class, 'colonne_id');
    }
}
