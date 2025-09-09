<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoumissionTableau extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'soumissions_tableau';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'valeur',
        'tableau_id',
        'structure_id',
        'assignation_id',
        'cellule_id'
    ];

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    public function assignation()
    {
        return $this->belongsTo(Assignation::class, 'assignation_id');
    }

    public function cellule()
    {
        return $this->belongsTo(Cellule::class, 'cellule_id');
    }
}
