<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegleDeGestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'libelle',
        'operations',
        'tableau_id'
    ];

    public function tableau()
    {
        return $this->belongsTo(Tableau::class, 'tableau_id');
    }
}
