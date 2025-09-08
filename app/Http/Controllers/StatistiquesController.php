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

        // Correction: Utiliser l'année de l'activité au lieu de created_at
        if ($annee) {
            $query->where('annee', $annee);
        }

        // Pour le trimestre, filtrer les activités qui ont cette période
        if ($trimestre) {
            $periodeName = 'T' . $trimestre;
            $query->whereHas('periodes', function ($q) use ($periodeName) {
                $q->where('libelle', $periodeName);
            });
        }

        $totalActivites = $query->count();

        $totalProgrammes = ProgrammeBudgetaire::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->where('id', $programmeBudgetaireId);
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
        })->count();

        $totalIndicateurs = IndicateurProgramme::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
        })->count();

        $totalStructures = Structure::count();
        $totalUtilisateurs = User::count();

        $totalSuivis = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
            // Format de période attendu dans la base
            $periode = $annee . '-T' . $trimestre;
            return $q->where('periode', 'LIKE', '%' . $periode . '%');
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

        if ($annee) {
            $query->where('annee', $annee);
        }

        if ($trimestre) {
            $periodeName = 'T' . $trimestre;
            $query->whereHas('periodes', function ($q) use ($periodeName) {
                $q->where('libelle', $periodeName);
            });
        }

        $budgetPrevu = $query->sum('cout_previsionnel');

        // Correction pour le budget exécuté
        $budgetExecute = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
            $periode = $annee . '-T' . $trimestre;
            return $q->where('periode', 'LIKE', '%' . $periode . '%');
        })->sum('cout_effectif');

        $budgetRestant = $budgetPrevu - $budgetExecute;
        $tauxExecution = $budgetPrevu > 0 ? round(($budgetExecute / $budgetPrevu) * 100, 2) : 0;

        // Budget par trimestre pour l'année donnée
        $budgetParTrimestre = [];
        for ($t = 1; $t <= 4; $t++) {
            $periodeName = 'T' . $t;
            $budget = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('id', $programmeBudgetaireId);
                });
            })
                ->where('annee', $annee)
                ->whereHas('periodes', function ($q) use ($periodeName) {
                    $q->where('libelle', $periodeName);
                })
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
        $baseQuery = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
        })->when($trimestre, function ($q) use ($trimestre) {
            $periodeName = 'T' . $trimestre;
            return $q->whereHas('periodes', function ($subQ) use ($periodeName) {
                $subQ->where('libelle', $periodeName);
            });
        });

        // Activités avec des suivis (en cours ou terminées)
        $activitesAvecSuivis = (clone $baseQuery)
            ->whereHas('objectifSpecifique.indicateursProgrammes.suivis', function ($q) use ($trimestre, $annee) {
                if ($trimestre && $annee) {
                    $periode = $annee . '-T' . $trimestre;
                    $q->where('periode', 'LIKE', '%' . $periode . '%');
                }
            })
            ->distinct()
            ->count();

        // Activités terminées (résultat >= 100%)
        $activitesTerminees = (clone $baseQuery)
            ->whereHas('objectifSpecifique.indicateursProgrammes.suivis', function ($q) use ($trimestre, $annee) {
                $q->whereNotNull('resultat_atteint')
                    ->where('resultat_atteint', '>=', 100);
                if ($trimestre && $annee) {
                    $periode = $annee . '-T' . $trimestre;
                    $q->where('periode', 'LIKE', '%' . $periode . '%');
                }
            })
            ->distinct()
            ->count();

        // Activités en cours (avec suivis mais pas terminées)
        $activitesEnCours = $activitesAvecSuivis - $activitesTerminees;

        // Activités non commencées (sans suivis)
        $activitesNonCommencees = (clone $baseQuery)
            ->whereDoesntHave('objectifSpecifique.indicateursProgrammes.suivis', function ($q) use ($trimestre, $annee) {
                if ($trimestre && $annee) {
                    $periode = $annee . '-T' . $trimestre;
                    $q->where('periode', 'LIKE', '%' . $periode . '%');
                }
            })
            ->count();

        // Activités en retard (résultat < 50%)
        $activitesEnRetard = (clone $baseQuery)
            ->whereHas('objectifSpecifique.indicateursProgrammes.suivis', function ($q) use ($trimestre, $annee) {
                $q->where('resultat_atteint', '<', 50);
                if ($trimestre && $annee) {
                    $periode = $annee . '-T' . $trimestre;
                    $q->where('periode', 'LIKE', '%' . $periode . '%');
                }
            })
            ->distinct()
            ->count();

        $totalActivites = $baseQuery->count();

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
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
        })->whereHas('suivis', function ($q) use ($trimestre, $annee) {
            if ($trimestre && $annee) {
                $periode = $annee . '-T' . $trimestre;
                $q->where('periode', 'LIKE', '%' . $periode . '%');
            }
        })->count();

        $totalIndicateurs = IndicateurProgramme::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
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
            })
                ->where('periode', 'LIKE', '%' . $periode . '%')
                ->whereNotNull('resultat_atteint')
                ->avg('resultat_atteint');

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
        })->when($annee, function ($q) use ($annee) {
            return $q->where('annee', $annee);
        })->when($trimestre, function ($q) use ($trimestre) {
            $periodeName = 'T' . $trimestre;
            return $q->whereHas('periodes', function ($subQ) use ($periodeName) {
                $subQ->where('libelle', $periodeName);
            });
        })->orderBy('cout_previsionnel', 'desc')
            ->with(['objectifSpecifique.action.objectifStrategique.programmeBudgetaire'])
            ->take(10)->get();
    }

    private function getChartData($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        // Evolution mensuelle des activités (basée sur l'année de l'activité)
        $evolutionActivites = [];
        for ($mois = 1; $mois <= 12; $mois++) {
            // Approximation: répartir les activités du trimestre sur les mois
            $trimestreCorrespondant = ceil($mois / 3);
            $periodeName = 'T' . $trimestreCorrespondant;

            $count = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
                return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                    $subQ->where('id', $programmeBudgetaireId);
                });
            })
                ->where('annee', $annee)
                ->whereHas('periodes', function ($q) use ($periodeName) {
                    $q->where('libelle', $periodeName);
                })
                ->count();

            $evolutionActivites[] = round($count / 3, 1); // Répartition approximative sur les 3 mois
        }

        // Répartition par statut
        $advancementStats = $this->getAdvancementStats($programmeBudgetaireId, $trimestre, $annee);
        $repartitionStatut = [
            $advancementStats['activites_non_commencees'],
            $advancementStats['activites_en_cours'],
            $advancementStats['activites_terminees']
        ];

        // Budget par programme - Méthode alternative plus simple
        $budgetProgrammesQuery = ProgrammeBudgetaire::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->where('id', $programmeBudgetaireId);
        })
            ->when($annee, function ($q) use ($annee) {
                return $q->where('annee', $annee);
            })
            ->take(10)
            ->get();

        // Calculer le budget total pour chaque programme
        $budgetProgrammes = $budgetProgrammesQuery->map(function ($programme) use ($trimestre, $annee) {
            $budgetQuery = Activite::whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($q) use ($programme) {
                $q->where('id', $programme->id);
            });

            if ($annee) {
                $budgetQuery->where('annee', $annee);
            }

            if ($trimestre) {
                $periodeName = 'T' . $trimestre;
                $budgetQuery->whereHas('periodes', function ($q) use ($periodeName) {
                    $q->where('libelle', $periodeName);
                });
            }

            $programme->budget_total = $budgetQuery->sum('cout_previsionnel');
            return $programme;
        });

        return [
            'evolution_activites' => $evolutionActivites,
            'repartition_statut' => $repartitionStatut,
            'labels_statut' => ['Non commencées', 'En cours', 'Terminées'],
            'budget_programmes' => [
                'labels' => $budgetProgrammes->pluck('libelle')->toArray(),
                'data' => $budgetProgrammes->pluck('budget_total')->toArray()
            ],
            'mois' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']
        ];
    }

    private function getAlerts($programmeBudgetaireId = null, $trimestre = null, $annee = null)
    {
        $alerts = [];

        // Activités sans suivi pour la période donnée
        $activitesSansSuivi = Activite::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })
            ->when($annee, function ($q) use ($annee) {
                return $q->where('annee', $annee);
            })
            ->when($trimestre, function ($q) use ($trimestre) {
                $periodeName = 'T' . $trimestre;
                return $q->whereHas('periodes', function ($subQ) use ($periodeName) {
                    $subQ->where('libelle', $periodeName);
                });
            })
            ->whereDoesntHave('objectifSpecifique.indicateursProgrammes.suivis', function ($q) use ($trimestre, $annee) {
                if ($trimestre && $annee) {
                    $periode = $annee . '-T' . $trimestre;
                    $q->where('periode', 'LIKE', '%' . $periode . '%');
                }
            })
            ->count();

        if ($activitesSansSuivi > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'bi bi-exclamation-triangle',
                'title' => 'Attention!',
                'message' => "$activitesSansSuivi activité(s) n'ont aucun suivi pour cette période."
            ];
        }

        // Budget dépassé - requête simplifiée
        $budgetDepasse = Suivi::when($programmeBudgetaireId, function ($q) use ($programmeBudgetaireId) {
            return $q->whereHas('indicateurProgramme.objectifStrategique.programmeBudgetaire', function ($subQ) use ($programmeBudgetaireId) {
                $subQ->where('id', $programmeBudgetaireId);
            });
        })
            ->when($trimestre && $annee, function ($q) use ($trimestre, $annee) {
                $periode = $annee . '-T' . $trimestre;
                return $q->where('periode', 'LIKE', '%' . $periode . '%');
            })
            ->whereHas('indicateurProgramme.activite', function ($q) {
                $q->whereRaw('suivis.cout_effectif > activites.cout_previsionnel');
            })
            ->count();

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