<?php

namespace App\Http\Controllers;

use App\Models\ObjectifsStrategique;
use App\Models\ProgrammesActivite;
use App\Models\Structure;
use App\Models\ProgrammeActivite;
use App\Models\ProgrammeBudgetaire;
use App\Models\ObjectifSpecifique;
use App\Models\Activite;
use App\Models\IndicateurProgramme;
use App\Models\Suivi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistiques générales
        $stats = $this->getGeneralStats();

        // Statistiques financières
        $financialStats = $this->getFinancialStats();

        // Données pour les graphiques
        $chartData = $this->getChartData();

        // Activités récentes
        $recentActivities = $this->getRecentActivities();

        // Indicateurs de performance
        $performanceIndicators = $this->getPerformanceIndicators();

        // Alertes et notifications
        $alerts = $this->getAlerts();

        return view('dashboard.index', compact(
            'stats',
            'financialStats',
            'chartData',
            'recentActivities',
            'performanceIndicators',
            'alerts'
        ));
    }

    public function statistique(): View
    {
        // Statistiques générales
        $stats = $this->getGeneralStats();

        // Statistiques financières
        $financialStats = $this->getFinancialStats();

        // Données pour les graphiques
        $chartData = $this->getChartData();

        // Activités récentes
        $recentActivities = $this->getRecentActivities();

        // Indicateurs de performance
        $performanceIndicators = $this->getPerformanceIndicators();

        // Alertes et notifications
        $alerts = $this->getAlerts();

        return view('dashboard.statistique', compact(
            'stats',
            'financialStats',
            'chartData',
            'recentActivities',
            'performanceIndicators',
            'alerts'
        ));
    }

    private function getGeneralStats(): array
    {
        return [
            'total_structures' => Structure::count(),
            'total_programmes_activites' => ProgrammesActivite::count(),
            'total_programmes_budgetaires' => ProgrammeBudgetaire::count(),
            'total_objectifs_strategiques' => ObjectifsStrategique::count(),
            'total_objectifs_specifiques' => ObjectifSpecifique::count(),
            'total_activites' => Activite::count(),
            'total_indicateurs' => IndicateurProgramme::count(),
            'total_utilisateurs' => User::count(),
            'total_suivis' => Suivi::count(),
        ];
    }

    private function getFinancialStats(): array
    {
        $totalPrevu = Activite::sum('cout_previsionnel');
        $totalEffectif = Suivi::sum('cout_effectif');
        $tauxExecution = $totalPrevu > 0 ? ($totalEffectif / $totalPrevu) * 100 : 0;

        return [
            'budget_prevu' => $totalPrevu,
            'budget_execute' => $totalEffectif,
            'budget_restant' => $totalPrevu - $totalEffectif,
            'taux_execution' => round($tauxExecution, 2),
        ];
    }

    private function getChartData(): array
    {
        // Données pour graphique des activités par période
        $activitesPeriode = Activite::selectRaw('periode, COUNT(*) as count')
            ->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->pluck('count', 'periode')
            ->toArray();

        // Données pour graphique des coûts par programme budgétaire
        $coutsProgrammes = ProgrammeBudgetaire::with(['objectifsStrategiques.actions.objectifsSpecifiques.activites'])
            ->get()
            ->map(function ($programme) {
                $totalCout = 0;
                foreach ($programme->objectifsStrategiques as $objectif) {
                    foreach ($objectif->actions as $action) {
                        foreach ($action->objectifsSpecifiques as $objSpec) {
                            $totalCout += $objSpec->activites->sum('cout_previsionnel');
                        }
                    }
                }
                return [
                    'nom' => $programme->libelle,
                    'cout' => $totalCout
                ];
            });

        // Évolution mensuelle des suivis
        $evolutionSuivis = Suivi::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as mois, COUNT(*) as count")
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->pluck('count', 'mois')
            ->toArray();

        return [
            'activites_periode' => $activitesPeriode,
            'couts_programmes' => $coutsProgrammes,
            'evolution_suivis' => $evolutionSuivis,
        ];
    }

    private function getRecentActivities(): array
    {
        return [
            'nouvelles_activites' => Activite::with('objectifSpecifique')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'derniers_suivis' => Suivi::with('indicateurProgramme')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'nouveaux_utilisateurs' => User::with('structure')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    private function getPerformanceIndicators(): array
    {
        // Calcul des indicateurs de performance
        $totalIndicateurs = IndicateurProgramme::count();
        $indicateursAvecSuivi = IndicateurProgramme::whereHas('suivis')->count();
        $tauxSuivi = $totalIndicateurs > 0 ? ($indicateursAvecSuivi / $totalIndicateurs) * 100 : 0;

        // Moyenne des résultats atteints
        $moyenneResultats = Suivi::whereNotNull('resultat_atteint')
            ->avg('resultat_atteint') ?? 0;

        // Nombre d'activités par période
        $activitesEnCours = Activite::where('periode', 'LIKE', '%' . date('Y') . '%')->count();

        return [
            'taux_suivi_indicateurs' => round($tauxSuivi, 2),
            'moyenne_resultats' => round($moyenneResultats, 2),
            'activites_annee_courante' => $activitesEnCours,
            'structures_actives' => Structure::whereHas('users')->count(),
        ];
    }

    private function getAlerts(): array
    {
        $alerts = [];

        // Alertes budgétaires
        $budgetAlert = $this->checkBudgetAlerts();
        if ($budgetAlert) {
            $alerts[] = $budgetAlert;
        }

        // Alertes de suivi
        $suiviAlert = $this->checkSuiviAlerts();
        if ($suiviAlert) {
            $alerts[] = $suiviAlert;
        }

        // Alertes d'activités
        $activiteAlert = $this->checkActiviteAlerts();
        if ($activiteAlert) {
            $alerts[] = $activiteAlert;
        }

        return $alerts;
    }

    private function checkBudgetAlerts(): ?array
    {
        $financialStats = $this->getFinancialStats();

        if ($financialStats['taux_execution'] > 90) {
            return [
                'type' => 'warning',
                'title' => 'Budget presque épuisé',
                'message' => 'Le taux d\'exécution budgétaire atteint ' . $financialStats['taux_execution'] . '%',
                'icon' => 'bi-exclamation-triangle'
            ];
        }

        return null;
    }

    private function checkSuiviAlerts(): ?array
    {
        $indicateursSansSuivi = IndicateurProgramme::whereDoesntHave('suivis')->count();

        if ($indicateursSansSuivi > 0) {
            return [
                'type' => 'info',
                'title' => 'Indicateurs sans suivi',
                'message' => $indicateursSansSuivi . ' indicateur(s) n\'ont pas encore de suivi',
                'icon' => 'bi-info-circle'
            ];
        }

        return null;
    }

    private function checkActiviteAlerts(): ?array
    {
        $activitesRecentes = Activite::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        if ($activitesRecentes == 0) {
            return [
                'type' => 'warning',
                'title' => 'Aucune nouvelle activité',
                'message' => 'Aucune activité créée cette semaine',
                'icon' => 'bi-calendar-x'
            ];
        }

        return null;
    }

    public function getStatsApi(Request $request)
    {
        $period = $request->get('period', '30'); // 30 jours par défaut

        $stats = [
            'activities' => Activite::where('created_at', '>=', Carbon::now()->subDays($period))->count(),
            'suivis' => Suivi::where('created_at', '>=', Carbon::now()->subDays($period))->count(),
            'budget' => Activite::where('created_at', '>=', Carbon::now()->subDays($period))->sum('cout_previsionnel'),
        ];

        return response()->json($stats);
    }
}