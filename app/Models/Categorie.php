<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Categorie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'libelle'
    ];

    public function tableaux()
    {
        return $this->hasMany(Tableau::class, 'categorie_id');
    }
}
