@extends('layouts.admin')
@section('breadcrumb', 'Statistiques')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/statistiques.css') }}" />
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Statistiques</h3>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="refreshStats">
                            <i class="bi bi-arrow-clockwise"></i> Actualiser
                        </button>
                        <button class="btn btn-outline-success btn-sm" id="exportStats">
                            <i class="bi bi-download"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" id="filers-section">
                        <h3 class="card-title">Filtres</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-bs-toggle="collapse"
                                data-bs-target="#filtersCollapse">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse show" id="filtersCollapse">
                        <div class="card-body">
                            <form method="GET" id="filterForm" class="d-flex flex-wrap align-items-end gap-2">
                                <div class="col-auto">
                                    <label for="programme_budgetaire_id" class="form-label">Programme budgétaire</label>
                                    <select name="programme_budgetaire_id" id="programme_budgetaire_id" class="form-select">
                                        <option value="">Tous les programmes</option>
                                        @foreach ($programmesBudgetaires as $programme)
                                            <option value="{{ $programme->id }}"
                                                {{ $programmeBudgetaireId == $programme->id ? 'selected' : '' }}>
                                                {{ $programme->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-auto">
                                    <label for="trimestre" class="form-label">Trimestre</label>
                                    <select name="trimestre" id="trimestre" class="form-select">
                                        <option value="">Tous les trimestres</option>
                                        <option value="1" {{ $trimestre == '1' ? 'selected' : '' }}>Trimestre 1
                                            (Jan-Mar)</option>
                                        <option value="2" {{ $trimestre == '2' ? 'selected' : '' }}>Trimestre 2
                                            (Avr-Jun)</option>
                                        <option value="3" {{ $trimestre == '3' ? 'selected' : '' }}>Trimestre 3
                                            (Jul-Sep)</option>
                                        <option value="4" {{ $trimestre == '4' ? 'selected' : '' }}>Trimestre 4
                                            (Oct-Déc)</option>
                                    </select>
                                </div>

                                <div class="col-auto">
                                    <label for="annee" class="form-label">Année</label>
                                    <select name="annee" id="annee" class="form-select">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel"></i> Appliquer
                                    </button>
                                    <a href="{{ route('statistiques.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        <!-- Statistiques générales -->
        <div class="row mb-4">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_activites']) }}</h3>
                        <p>Activités</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_programmes']) }}</h3>
                        <p>Programmes</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-folder"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_indicateurs']) }}</h3>
                        <p>Indicateurs</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_structures']) }}</h3>
                        <p>Structures</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_utilisateurs']) }}</h3>
                        <p>Utilisateurs</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_suivis']) }}</h3>
                        <p>Suivis</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques d'avancement -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Non demarrées</h5>
                        <h3 class="text-primary">{{ number_format($advancementStats['activites_non_commencees']) }}</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-primary"
                                style="width: {{ $advancementStats['pourcentage_non_commencees'] }}%">
                                {{ $advancementStats['pourcentage_non_commencees'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">En cours</h5>
                        <h3 class="text-warning">{{ number_format($advancementStats['activites_en_cours']) }}</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-warning"
                                style="width: {{ $advancementStats['pourcentage_en_cours'] }}%">
                                {{ $advancementStats['pourcentage_en_cours'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">Achevées</h5>
                        <h3 class="text-success">{{ number_format($advancementStats['activites_terminees']) }}</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success"
                                style="width: {{ $advancementStats['pourcentage_terminees'] }}%">
                                {{ $advancementStats['pourcentage_terminees'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3 text-center">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="circle-progress" data-value="{{ $performanceStats['taux_suivi_indicateurs'] }}">
                            <span>{{ $performanceStats['taux_suivi_indicateurs'] }}%</span>
                        </div>
                        <p class="">Taux de suivi des indicateurs</p>
                    </div>
                </div>
            </div> --}}
            {{--             <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-danger">En retard</h5>
                        <h3 class="text-danger">{{ number_format($advancementStats['activites_en_retard']) }}</h3>
                        <small class="text-muted">Activités avec performance < 50%</small>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Statistiques financières -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Coût prévisionnel</h5>
                        <h3 class="text-primary">{{ number_format($financialStats['budget_prevu'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">Coût effectif</h5>
                        <h3 class="text-success">{{ number_format($financialStats['budget_execute'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Taux d'éxecution physique</h5>
                        <h3 class="text-warning">{{ $performanceStats['moyenne_resultats'] }} %
                        </h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-warning"
                                style="width: {{ $performanceStats['moyenne_resultats'] }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Coût restant</h5>
                        <h3 class="text-warning">{{ number_format($financialStats['budget_restant'], 0, ',', ' ') }} FCFA
                        </h3>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">Taux d'exécution financière</h5>
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
        {{-- <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Indicateurs de performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="circle-progress"
                                    data-value="{{ $performanceStats['taux_suivi_indicateurs'] }}">
                                    <span>{{ $performanceStats['taux_suivi_indicateurs'] }}%</span>
                                </div>
                                <p class="mt-2">Taux de suivi des indicateurs</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="circle-progress" data-value="{{ $performanceStats['moyenne_resultats'] }}">
                                    <span>{{ $performanceStats['moyenne_resultats'] }}%</span>
                                </div>
                                <p class="mt-2">Moyenne des résultats</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h4 class="text-success">{{ $performanceStats['resultats_excellents'] }}</h4>
                                <p>Excellents (≥90%)</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h4 class="text-warning">{{ $performanceStats['resultats_satisfaisants'] }}</h4>
                                <p>Satisfaisants (70-89%)</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h4 class="text-danger">{{ $performanceStats['resultats_faibles'] }}</h4>
                                <p>Faibles (<70%)< /p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Graphiques -->
        <div class="row mb-4">
            <!-- Graphique d'évolution des activités -->
            {{-- <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Évolution mensuelle des activités</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="evolutionActivitesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div> --}}

            <!-- Graphique de répartition par statut -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Répartition des activités par statut</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="repartitionStatutChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget par trimestre -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Coût par trimestre</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="budgetTrimestreChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance par trimestre -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Performance par trimestre</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrimestreChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget par programme -->
        @if (count($chartData['budget_programmes']['labels']) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Coût par programme budgétaire</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="budgetProgrammesChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tableaux détaillés -->
        {{-- <div class="row">
            <!-- Top activités les plus coûteuses -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 - Activités les plus coûteuses</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" id="maxHeightStatistique">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th>Programme</th>
                                        <th class="text-end">Coût</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topActivites as $activite)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($activite->libelle, 40) }}</strong><br>
                                                <small class="text-muted">{{ $activite->periode }}</small>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($activite->objectifSpecifique->action->objectifStrategique->programmeBudgetaire->libelle ?? 'N/A', 30) }}</small>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-primary">{{ number_format($activite->cout_previsionnel, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Aucune activité trouvée</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques par structure -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Statistiques par structure</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Structure</th>
                                        <th>Type</th>
                                        <th class="text-end">Utilisateurs</th>
                                        <th class="text-center">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($structureStats['details_structures'] as $structure)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($structure->nom, 25) }}</strong><br>
                                                <small class="text-muted">{{ $structure->sigle }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $structure->type_structure }}</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-info">{{ $structure->users_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($structure->users_count > 0)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Aucune structure trouvée
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row text-center">
                            <div class="col-6">
                                <strong>{{ $structureStats['structures_actives'] }}</strong><br>
                                <small>Actives</small>
                            </div>
                            <div class="col-6">
                                <strong>{{ $structureStats['structures_inactives'] }}</strong><br>
                                <small>Inactives</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Résumé financier par trimestre -->
        {{-- <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Résumé financier par trimestre</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($financialStats['budget_par_trimestre'] as $trimestre => $budget)
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $trimestre }}</h5>
                                            <h4 class="text-primary">{{ number_format($budget, 0, ',', ' ') }} FCFA</h4>
                                            <small class="text-muted">
                                                {{ $financialStats['budget_prevu'] > 0 ? round(($budget / $financialStats['budget_prevu']) * 100, 1) : 0 }}%
                                                du total
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </section>

    <!-- Scripts pour les graphiques -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        const statistiquesData = {
            chartData: @json($chartData ?? []),
            financialStats: @json($financialStats ?? []),
            performanceStats: @json($performanceStats ?? [])
        };

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (typeof Chart !== 'undefined') {
                    initStatistiquesCharts(statistiquesData);
                } else {
                    console.error('Chart.js non disponible');
                }
            }, 100);
        });
    </script>
    <script src="{{ asset('js/statistiques.js') }}"></script>
@endsection
