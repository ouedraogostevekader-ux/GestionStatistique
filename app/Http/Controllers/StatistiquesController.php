<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activite;
use App\Models\ProgrammeBudgetaire;
use App\Models\Suivi;
use App\Models\IndicateurProgramme;
use App\Models\Structure;
use App\Models\User;
use App\Models\ObjectifStrategique;
use App\Models\Action;
use App\Models\ObjectifSpecifique;
use App\Models\ProgrammeActivite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatistiquesController extends Controller
{
    public function index(Request $request)
    {
        $programmeBudgetaireId = $request->get('programme_budgetaire_id');
        $trimestre = $request->get('trimestre');
        $annee = $request->get('annee', date('Y'));

        // Récupération des programmes budgétaires pour le filtre
        $programmesBudgetaires = ProgrammeBudgetaire::orderBy('libelle')->get();

        // Statistiques générales
        $stats = $this->getGeneralStats($programmeBudgetaireId, $trimestre, $annee);

        // Statistiques financières
        $financialStats = $this->getFinancialStats($programmeBudgetaireId, $trimestre, $annee);

        // Statistiques d'avancement
        $advancementStats = $this->getAdvancementStats($programmeBudgetaireId, $trimestre, $annee);

        // Statistiques par structure
        $structureStats = $this->getStructureStats($programmeBudgetaireId, $trimestre, $annee);

        // Données pour les graphiques
        $chartData = $this->getChartData($programmeBudgetaireId, $trimestre, $annee);

        // Statistiques de performance
        $performanceStats = $this->getPerformanceStats($programmeBudgetaireId, $trimestre, $annee);

        // Top activités les plus coûteuses
        $topActivites = $this->getTopActivites($programmeBudgetaireId, $trimestre, $annee);

        // Alertes et notifications
        $alerts = $this->getAlerts($programmeBudgetaireId, $trimestre, $annee);

        return view('dashboard.statistique', compact(
            'stats',
            'financialStats',
            'advancementStats',
            'structureStats',
            'chartData',
            'performanceStats',
            'topActivites',
            'alerts',
            'programmesBudgetaires',
            'programmeBudgetaireId',
            'trimestre',
            'annee'
        ));
    }

    private function getGeneralStats($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $query = Activite::query();

        if ($programmeBudgetaireId) {
            $query->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($q) use ($programmeBudgetaireId) {
                $q->where('id', $programmeBudgetaireId);
            });
        }

        if ($trimestre && $annee) {
            $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        } elseif ($annee) {
            $query->whereYear('created_at', $annee);
        }

        $totalActivites = $query->count();
        $totalProgrammes = ProgrammeBudgetaire::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->where('id', $programmeBudgetaireId);
        })->count();

        $totalIndicateurs = IndicateurProgramme::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->count();

        $totalStructures = Structure::count();
        $totalUtilisateurs = User::count();
        $totalSuivis = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->count();

        return [
            'total_activites' => $totalActivites,
            'total_programmes' => $totalProgrammes,
            'total_indicateurs' => $totalIndicateurs,
            'total_structures' => $totalStructures,
            'total_utilisateurs' => $totalUtilisateurs,
            'total_suivis' => $totalSuivis,
        ];
    }

    private function getFinancialStats($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $query = Activite::query();

        if ($programmeBudgetaireId) {
            $query->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($q) use ($programmeBudgetaireId) {
                $q->where('id', $programmeBudgetaireId);
            });
        }

        if ($trimestre && $annee) {
            $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        } elseif ($annee) {
            $query->whereYear('created_at', $annee);
        }

        $budgetPrevu = $query->sum('cout_previsionnel');

        $budgetExecute = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->sum('cout_effectif');

        $budgetRestant = $budgetPrevu - $budgetExecute;
        $tauxExecution = $budgetPrevu > 0 ? round(($budgetExecute / $budgetPrevu) * 100, 2) : 0;

        // Budget par trimestre
        $budgetParTrimestre = [];
        for ($t = 1; $t <= 4; $t++) {
            $dateRange = $this->getTrimestreDateRange($t, $annee);
            $budget = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('id', $programmeBudgetaireId);
                });
            })->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('cout_previsionnel');

            $budgetParTrimestre["T$t"] = $budget;
        }

        return [
            'budget_prevu' => $budgetPrevu,
            'budget_execute' => $budgetExecute,
            'budget_restant' => $budgetRestant,
            'taux_execution' => $tauxExecution,
            'budget_par_trimestre' => $budgetParTrimestre,
        ];
    }

    private function getAdvancementStats($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $currentDate = Carbon::now();

        // Activités en cours (période contient l'année courante)
        $activitesEnCours = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->where('periode', 'LIKE', '%' . $currentDate->year . '%')
            ->when($trimestre, function ($q) use ($trimestre) {
                return $q->where('periode', 'LIKE', '%T' . $trimestre . '%');
            })->count();

        // Activités terminées (avec suivis complets)
        $activitesTerminees = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereHas('objectifSpecifique.indicateursProgrammes.suivis', function ($q) {
            $q->whereNotNull('resultat_atteint')->where('resultat_atteint', '>=', 100);
        })->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
            $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
            return $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->count();

        // Activités non commencées (sans suivis)
        $activitesNonCommencees = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereDoesntHave('objectifSpecifique.indicateursProgrammes.suivis')
            ->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
                $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
                return $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })->count();

        // Activités en retard
        $activitesEnRetard = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereHas('objectifSpecifique.indicateursProgrammes.suivis', function ($q) {
            $q->where('resultat_atteint', '<', 50);
        })->count();

        $totalActivites = $activitesEnCours + $activitesTerminees + $activitesNonCommencees;

        return [
            'activites_en_cours' => $activitesEnCours,
            'activites_terminees' => $activitesTerminees,
            'activites_non_commencees' => $activitesNonCommencees,
            'activites_en_retard' => $activitesEnRetard,
            'total_activites' => $totalActivites,
            'pourcentage_en_cours' => $totalActivites > 0 ? round(($activitesEnCours / $totalActivites) * 100, 1) : 0,
            'pourcentage_terminees' => $totalActivites > 0 ? round(($activitesTerminees / $totalActivites) * 100, 1) : 0,
            'pourcentage_non_commencees' => $totalActivites > 0 ? round(($activitesNonCommencees / $totalActivites) * 100, 1) : 0,
        ];
    }

    private function getStructureStats($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $structures = Structure::withCount(['users'])
            ->when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('programmesBudgetaires', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('programme_budgetaire_id', $programmeBudgetaireId);
                });
            })->get();

        $structuresActives = $structures->filter(function ($structure) {
            return $structure->users_count > 0;
        })->count();

        return [
            'total_structures' => $structures->count(),
            'structures_actives' => $structuresActives,
            'structures_inactives' => $structures->count() - $structuresActives,
            'moyenne_utilisateurs_structure' => $structures->count() > 0 ? round($structures->sum('users_count') / $structures->count(), 1) : 0,
            'details_structures' => $structures->take(10),
        ];
    }

    private function getPerformanceStats($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $suivis = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
            $periode = $annee . '-T' . $trimestre;
            return $q->where('periode', 'LIKE', '%' . $periode . '%');
        })->whereNotNull('resultat_atteint')->get();

        $moyenneResultats = $suivis->count() > 0 ? round($suivis->avg('resultat_atteint'), 1) : 0;

        $indicateursAvecSuivi = IndicateurProgramme::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereHas('suivis')->count();

        $totalIndicateurs = IndicateurProgramme::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->count();

        $tauxSuiviIndicateurs = $totalIndicateurs > 0 ? round(($indicateursAvecSuivi / $totalIndicateurs) * 100, 1) : 0;

        // Performance par trimestre
        $performanceParTrimestre = [];
        for ($t = 1; $t <= 4; $t++) {
            $periode = $annee . '-T' . $t;
            $suivisTrimestre = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('id', $programmeBudgetaireId);
                });
            })->where('periode', 'LIKE', '%' . $periode . '%')
                ->whereNotNull('resultat_atteint')->avg('resultat_atteint');

            $performanceParTrimestre["T$t"] = round($suivisTrimestre ?: 0, 1);
        }

        return [
            'moyenne_resultats' => $moyenneResultats,
            'taux_suivi_indicateurs' => $tauxSuiviIndicateurs,
            'total_suivis' => $suivis->count(),
            'performance_par_trimestre' => $performanceParTrimestre,
            'resultats_excellents' => $suivis->where('resultat_atteint', '>=', 90)->count(),
            'resultats_satisfaisants' => $suivis->whereBetween('resultat_atteint', [70, 89])->count(),
            'resultats_faibles' => $suivis->where('resultat_atteint', '<', 70)->count(),
        ];
    }

    private function getTopActivites($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        return Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
            $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
            return $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->orderBy('cout_previsionnel', 'desc')
            ->with(['objectifSpecifique.action.objectifStrategique.programmeBudgetaire'])
            ->take(10)->get();
    }

    private function getChartData($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        // Evolution mensuelle des activités
        $evolutionActivites = [];
        for ($mois = 1; $mois <= 12; $mois++) {
            $count = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('id', $programmeBudgetaireId);
                });
            })->whereYear('created_at', $annee)
                ->whereMonth('created_at', $mois)->count();

            $evolutionActivites[] = $count;
        }

        // Répartition par statut
        $advancementStats = $this->getAdvancementStats($programmeBudgetaireId, $trimestre, $annee);
        $repartitionStatut = [
            $advancementStats['activites_non_commencees'],
            $advancementStats['activites_en_cours'],
            $advancementStats['activites_terminees']
        ];

        // Budget par programme
        $budgetProgrammes = ProgrammeBudgetaire::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->where('id', $programmeBudgetaireId);
        })
            ->withSum([
                'activites' => function ($q) use ($trimestre, $annee) {
                    if ($trimestre && $annee) {
                        $dateRange = $this->getTrimestreDateRange($trimestre, $annee);
                        $q->whereBetween('activites.created_at', [$dateRange['start'], $dateRange['end']]);
                    }
                }
            ], 'cout_previsionnel')
            ->take(10)
            ->get();

        return [
            'evolution_activites' => $evolutionActivites,
            'repartition_statut' => $repartitionStatut,
            'labels_statut' => ['Non commencées', 'En cours', 'Terminées'],
            'budget_programmes' => [
                'labels' => $budgetProgrammes->pluck('libelle')->toArray(),
                'data' => $budgetProgrammes->pluck('objectifs_strategiques_sum_cout_previsionnel')->toArray()
            ],
            'mois' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
        ];
    }

    private function getAlerts($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $alerts = [];

        // Activités sans suivi
        $activitesSansSuivi = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereDoesntHave('objectifSpecifique.indicateursProgrammes.suivis')->count();

        if ($activitesSansSuivi > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi bi-exclamation-triangle',
                'title' => 'Attention!',
                'message' => "$activitesSansSuivi activité(s) n'ont aucun suivi."
            ];
        }

        // Budget dépassé
        $budgetDepasse = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->whereRaw('cout_effectif > (
    SELECT a.cout_previsionnel 
    FROM activites a
    JOIN objectifs_specifiques os ON os.id = a.objectif_specifique_id
    JOIN indicateurs_programmes ip ON ip.objectif_specifique_id = os.id
    WHERE ip.id = suivis.indicateur_programme_id
    LIMIT 1
)')->count();

        if ($budgetDepasse > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'bi bi-currency-dollar',
                'title' => 'Budget dépassé!',
                'message' => "$budgetDepasse activité(s) ont dépassé leur budget prévisionnel."
            ];
        }

        return $alerts;
    }

    private function getTrimestreDateRange($trimestre, $annee)
    {
        switch ($trimestre) {
            case 1:
                return [
                    'start' => Carbon::create($annee, 1, 1)->startOfDay(),
                    'end' => Carbon::create($annee, 3, 31)->endOfDay()
                ];
            case 2:
                return [
                    'start' => Carbon::create($annee, 4, 1)->startOfDay(),
                    'end' => Carbon::create($annee, 6, 30)->endOfDay()
                ];
            case 3:
                return [
                    'start' => Carbon::create($annee, 7, 1)->startOfDay(),
                    'end' => Carbon::create($annee, 9, 30)->endOfDay()
                ];
            case 4:
                return [
                    'start' => Carbon::create($annee, 10, 1)->startOfDay(),
                    'end' => Carbon::create($annee, 12, 31)->endOfDay()
                ];
            default:
                return [
                    'start' => Carbon::create($annee, 1, 1)->startOfDay(),
                    'end' => Carbon::create($annee, 12, 31)->endOfDay()
                ];
        }
    }
}