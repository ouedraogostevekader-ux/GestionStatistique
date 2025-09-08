<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Structure extends Model
{
    use HasFactory, Notifiable;
    use LogsActivity;
    use HasUlids;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'sigle',
        'type_structure',
    ];

    public function tableaus()
    {
        return $this->hasMany(Tableau::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
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
        return $this->belongsToMany(ProgrammeBudgetaire::class, 'programme_budgetaire_structure');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Structure')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
