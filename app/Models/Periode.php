<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periode extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

    protected $table = 'periodes';
    protected $keyType = 'string'; // car ULID

    protected $fillable = ['libelle'];

    public function activites()
    {
        return $this->belongsToMany(Activite::class, 'activite_periode')->withPivot('annee')->withTimestamps();
    }
}