<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class ProgrammesActivite extends Model
{
    use HasFactory, SoftDeletes;
    use HasUlids, LogsActivity;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle',
        'annee',
        'file',
        'date_generation',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_generation' => 'datetime',
    ];

    public function programmesBudgetaires()
    {
        return $this->hasMany(ProgrammeBudgetaire::class, 'programme_activite_id', 'id');
    }

    public function rapportsActivites()
    {
        return $this->hasMany(RapportActivite::class);
    }

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Programme Activite')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
