<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Activite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activites';
    protected $keyType = 'string'; // car ULID
    public $incrementing = false;

    protected $fillable = [
        'cout_previsionnel',
        'periode',
        'libelle',
        'objectif_specifique_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid();
            }
        });
    }

    /**
     * Relation avec ObjectifSpecifique
     */
    public function objectifSpecifique()
    {
        return $this->belongsTo(ObjectifSpecifique::class);
    }

    /**
     * Relation avec IndicateurProgramme
     */
    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'activites_id', 'id');
    }
}
