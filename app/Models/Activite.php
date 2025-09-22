<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Session;
class Activite extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    use HasRoles;
    protected $table = 'activites';
    protected $keyType = 'string'; // car ULID
    public $incrementing = false;

    protected $fillable = [
        'cout_previsionnel',
        'libelle',
        'created_by',
        'updated_by',
        'objectif_specifique_id',
        'hors_programme'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::ulid();
                $model->created_by = Auth::user()->id;
            }
        });

        // Lors de la mise à jour
        static::updating(function ($model) {
            /* if ($model->created_by !== Auth::id()) {
                // Redirection avec message
                Session::flash('error', "Vous n'êtes pas autorisé à modifier cette activité.");
                throw new \Illuminate\Validation\ValidationException(
                    validator: \Illuminate\Support\Facades\Validator::make([], []),
                    response: redirect()->back()
                );
            } */
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            if ($model->created_by !== Auth::id()) {
                Session::flash('error', "Vous n'êtes pas autorisé à supprimer cette activité.");
                throw new \Illuminate\Validation\ValidationException(
                    validator: \Illuminate\Support\Facades\Validator::make([], []),
                    response: redirect()->back()
                );
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
    public function periodes()
    {
        return $this->belongsToMany(Periode::class, 'activite_periode')->withPivot('annee');
    }

    public function indicateurs()
    {
        return $this->hasMany(IndicateurProgramme::class, 'activites_id');
    }

    public function indicateursProgrammes()
    {
        return $this->hasMany(IndicateurProgramme::class, 'activites_id', 'id');
    }

    /* Vérifier si une activité est modifiable */
    public function activiteIsEdit()
    {
        // Vérifier si le programme budgétaire est actif
        return $this->objectifSpecifique->action->objectifStrategique->programmeBudgetaire->isActive;
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
            ->useLogName('Activité')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::addGlobalScope('nonHorsProgramme', function ($builder) {
            $builder->where('hors_programme', false);
        });
        static::deleting(function ($activite) {
            if ($activite->isForceDeleting()) {
                $activite->indicateursProgrammes()->withTrashed()->forceDelete();
                $activite->periodes()->detach();
            } else {
                $activite->indicateursProgrammes()->delete();
            }
        });

        static::restoring(function ($activite) {
            $activite->indicateursProgrammes()->withTrashed()->restore();
        });
    }
    public function scopeWithHorsProgramme($query)
    {
        return $query->withoutGlobalScope('nonHorsProgramme');
    }

    public function scopeOnlyHorsProgramme($query)
    {
        return $query->withoutGlobalScope('nonHorsProgramme')
            ->where('hors_programme', true);
    }

    /**
     * Extrait le numéro de version du libellé
     * Gère les formats :
     * - "Activité 1.1.1.3.30 : ..."
     * - "1.1.1.7 : ..."
     * 
     * @param string $libelle
     * @return string|null
     */
    private function extractVersionFromLibelle($libelle)
    {
        // Regex pour capturer le pattern de version (1.1 ou 1.1.1 ou 1.1.1.1 etc.)
        // Cherche une séquence de nombres séparés par des points
        $pattern = '/(\d+(?:\.\d+)*)/';
        
        if (preg_match($pattern, $libelle, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Parse une version string en array d'entiers
     * 
     * @param string $version
     * @return array
     */
    private function parseVersion($version)
    {
        if (!$version) {
            return [0];
        }
        return array_map('intval', explode('.', $version));
    }
    
    /**
     * Compare deux versions parsées
     * 
     * @param array $versionA
     * @param array $versionB
     * @return int -1 si A < B, 0 si A == B, 1 si A > B
     */
    private function compareVersions($versionA, $versionB)
    {
        $maxLength = max(count($versionA), count($versionB));
        
        for ($i = 0; $i < $maxLength; $i++) {
            $partA = $versionA[$i] ?? 0;
            $partB = $versionB[$i] ?? 0;
            
            if ($partA < $partB) {
                return -1;
            } elseif ($partA > $partB) {
                return 1;
            }
        }
        
        return 0;
    }
    
    /**
     * Scope pour ordonner par le numéro dans le libellé
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction Direction du tri ('asc' ou 'desc')
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeOrderByLibelle($query, $direction = 'asc')
    {
        $results = $query->get();
        
        $sorted = $results->sort(function ($a, $b) use ($direction) {
            // Extraire les versions des libellés
            $versionA = $this->extractVersionFromLibelle($a->libelle);
            $versionB = $this->extractVersionFromLibelle($b->libelle);
            
            // Si l'un des deux n'a pas de version, le mettre à la fin
            if (!$versionA && !$versionB) return 0;
            if (!$versionA) return 1;
            if (!$versionB) return -1;
            
            // Parser et comparer les versions
            $parsedA = $this->parseVersion($versionA);
            $parsedB = $this->parseVersion($versionB);
            
            $comparison = $this->compareVersions($parsedA, $parsedB);
            
            return $direction === 'desc' ? -$comparison : $comparison;
        });
        
        return $sorted->values();
    }
}
