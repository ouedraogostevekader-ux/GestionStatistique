<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\LogOptions;

class RapportActivite extends Authenticatable
{

    use HasFactory, SoftDeletes, HasUlids;
    protected $keyType = 'string';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'programme_activite_id',
        'libelle',
        'periode_rapport',
        'date_rapport',
        'file',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'periode_rapport' => 'datetime',
            'date_rapport' => 'datetime',
        ];
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    //Methode pour les logs
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return 'Creation';
        }

        if ($eventName == 'updated') {
            return 'Modification';
        }

        if ($eventName == 'deleted') {
            return 'Suppression';
        }

        return "{$eventName}";
    }

    public function programmesBudgetaires()
    {
        return $this->hasMany(ProgrammeBudgetaire::class);
    }

    public function rapportsActivites()
    {
        return $this->hasMany(RapportActivite::class);
    }
    public function programmeActivite()
    {
        return $this->belongsTo(ProgrammesActivite::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('User')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
