<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use LogsActivity;
    use HasUlids;
    use CausesActivity;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'matricule',
        'nom',
        'prenom',
        'fonction',
        'structure_id',
        'is_active'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function getNomPrenomAttribute()
    {
        return $this->nom . " " . $this->prenom;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('User')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function programmesBudgetaires()
    {
        return $this->belongsToMany(ProgrammeBudgetaire::class, 'responsables')->withPivot('date_debut', 'date_fin');
    }

    /* Role administrateur */
    public function isAdmin()
    {
        return $this->hasRole('Admin') || $this->hasRole('Super-Admin');
    }

    public function roleLabels(): string
    {
        if ($this->roles->isEmpty()) {
            return 'Aucun rôle';
        }

        return $this->roles->map(function ($role) {
            if (!empty($role->name)) {
                return $role->name;
            }

            return Str::of($role->name)
                ->replace(['-', '_'], ' ')
                ->title()
                ->toString();
        })->implode(', ');
    }
}
