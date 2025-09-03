@extends('layouts.admin')
@section('breadcrumb', 'Tableau de bord')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Tableau de bord</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="refreshDashboard">
                            <i class="bi bi-arrow-clockwise"></i> Actualiser
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-calendar3"></i> Période
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-period="7">7 derniers jours</a></li>
                                <li><a class="dropdown-item" href="#" data-period="30">30 derniers jours</a></li>
                                <li><a class="dropdown-item" href="#" data-period="90">3 derniers mois</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <!-- Alertes -->
        @if (!empty($alerts))
            <div class="row mb-3">
                <div class="col-12">
                    @foreach ($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                            <i class="{{ $alert['icon'] }}"></i>
                            <strong>{{ $alert['title'] }}</strong> {{ $alert['message'] }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_activites']) }}</h3>
                        <p>Activités</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <a href="{{ route('activites.index') }}" class="small-box-footer">
                        Plus d'infos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_programmes_budgetaires']) }}</h3>
                        <p>Programmes budgétaires</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-folder"></i>
                    </div>
                    <a href="{{ route('programmebudgetaire.index') }}" class="small-box-footer">
                        Plus d'infos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_indicateurs']) }}</h3>
                        <p>Indicateurs</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <a href="{{ route('indicateurs-programmes.index') }}" class="small-box-footer">
                        Plus d'infos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_structures']) }}</h3>
                        <p>Structures</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <a href="{{ route('structures.index') }}" class="small-box-footer">
                        Plus d'infos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques financières -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Budget prévisionnel</h5>
                        <h3 class="text-primary">{{ number_format($financialStats['budget_prevu'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">Budget exécuté</h5>
                        <h3 class="text-success">{{ number_format($financialStats['budget_execute'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Budget restant</h5>
                        <h3 class="text-warning">{{ number_format($financialStats['budget_restant'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">Taux d'exécution</h5>
                        <h3 class="text-info">{{ $financialStats['taux_execution'] }}%</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-info" style="width: {{ $financialStats['taux_execution'] }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicateurs de performance -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Indicateurs de performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="circle-progress"
                                    data-value="{{ $performanceIndicators['taux_suivi_indicateurs'] }}">
                                    <span>{{ $performanceIndicators['taux_suivi_indicateurs'] }}%</span>
                                </div>
                                <p class="mt-2">Taux de suivi des indicateurs</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="circle-progress"
                                    data-value="{{ $performanceIndicators['moyenne_resultats'] }}">
                                    <span>{{ $performanceIndicators['moyenne_resultats'] }}%</span>
                                </div>
                                <p class="mt-2">Moyenne des résultats</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-primary">{{ $performanceIndicators['activites_annee_courante'] }}</h4>
                                <p>Activités année courante</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-success">{{ $performanceIndicators['structures_actives'] }}</h4>
                                <p>Structures actives</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row mb-4">
            <!-- Graphique des activités par période -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activités par période</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="activitesPeriodeChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique d'évolution des suivis -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Évolution des suivis</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="evolutionSuivisChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des coûts par programme -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Répartition budgétaire par programme</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="coutsProgrammesChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités récentes et tableau de bord détaillé -->
        <div class="row">
            <!-- Activités récentes -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activités récentes</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach ($recentActivities['nouvelles_activites'] as $activite)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($activite->libelle, 30) }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $activite->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-primary">{{ number_format($activite->cout_previsionnel, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('activites.index') }}" class="btn btn-sm btn-outline-primary">Voir toutes</a>
                    </div>
                </div>
            </div>

            <!-- Derniers suivis -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Derniers suivis</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach ($recentActivities['derniers_suivis'] as $suivi)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($suivi->indicateurProgramme->libelle ?? 'N/A', 30) }}</strong><br>
                                                <small class="text-muted">{{ $suivi->periode }}</small>
                                            </td>
                                            <td class="text-end">
                                                @if ($suivi->resultat_atteint)
                                                    <span class="badge bg-success">{{ $suivi->resultat_atteint }}%</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('suivis.index') }}" class="btn btn-sm btn-outline-primary">Voir tous</a>
                    </div>
                </div>
            </div>

            <!-- Nouveaux utilisateurs -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Nouveaux utilisateurs</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach ($recentActivities['nouveaux_utilisateurs'] as $user)
                                        <tr>
                                            <td>
                                                <strong>{{ $user->prenom }} {{ $user->nom }}</strong><br>
                                                <small class="text-muted">{{ $user->structure->nom ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">Voir tous</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts pour les graphiques -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Données pour les graphiques
        const chartData = @json($chartData);

        // Initialiser les graphiques au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            initDashboardCharts(chartData);
        });
    </script>
@endsection
