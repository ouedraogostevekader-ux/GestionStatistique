{{-- resources/views/programmes/suivi.blade.php --}}
@extends('layouts.admin')


@section('styles')
    <style>
        .performance-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }

        .indicator-progress {
            min-height: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .status-badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }

        .suivi-table {
            font-size: 13px;
        }

        .suivi-table th {
            background-color: #343a40;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        .alert-performance {
            border-left: 4px solid #ffc107;
            background-color: #fff3cd;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .chart-container {
                height: 200px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Navigation -->
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <a href="{{ route('programme-activites.show', $programme->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('programme-activites.tableau', $programme->id) }}" class="btn btn-info">
                            <i class="fas fa-table"></i> Vue Tableau
                        </a>
                    </div>
                    <div class="btn-group">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                        <button onclick="exportSuiviToExcel()" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- En-tête Programme -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h1 class="mb-0">
                            <i class="fas fa-chart-line"></i>
                            Suivi Performance - Programme {{ $programme->numero_programme }}
                        </h1>
                        <p class="mb-0">{{ $programme->libelle }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard de performance global -->
        <div class="row mb-4">
            @php
                $totalIndicateurs = 0;
                $indicateursAvecSuivi = 0;
                $totalSuivis = 0;

                foreach ($programme->objectifsStrategiques as $objectif) {
                    $totalIndicateurs += $objectif->indicateurs->count();
                    foreach ($objectif->indicateurs as $indicateur) {
                        if ($indicateur->suivis->count() > 0) {
                            $indicateursAvecSuivi++;
                            $totalSuivis += $indicateur->suivis->count();
                        }
                    }
                    foreach ($objectif->actions as $action) {
                        foreach ($action->objectifsSpecifiques as $objSpec) {
                            $totalIndicateurs += $objSpec->indicateurs->count();
                            foreach ($objSpec->indicateurs as $indicateur) {
                                if ($indicateur->suivis->count() > 0) {
                                    $indicateursAvecSuivi++;
                                    $totalSuivis += $indicateur->suivis->count();
                                }
                            }
                            foreach ($objSpec->activites as $activite) {
                                $totalIndicateurs += $activite->indicateurs->count();
                                foreach ($activite->indicateurs as $indicateur) {
                                    if ($indicateur->suivis->count() > 0) {
                                        $indicateursAvecSuivi++;
                                        $totalSuivis += $indicateur->suivis->count();
                                    }
                                }
                            }
                        }
                    }
                }

                $tauxSuivi = $totalIndicateurs > 0 ? round(($indicateursAvecSuivi / $totalIndicateurs) * 100, 1) : 0;
            @endphp

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-bar fa-2x mr-3"></i>
                            <div>
                                <div class="text-value-lg">{{ $totalIndicateurs }}</div>
                                <div>Total Indicateurs</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x mr-3"></i>
                            <div>
                                <div class="text-value-lg">{{ $indicateursAvecSuivi }}</div>
                                <div>Avec Suivi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-percentage fa-2x mr-3"></i>
                            <div>
                                <div class="text-value-lg">{{ $tauxSuivi }}%</div>
                                <div>Taux de Suivi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list fa-2x mr-3"></i>
                            <div>
                                <div class="text-value-lg">{{ $totalSuivis }}</div>
                                <div>Total Suivis</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerte performance -->
        @if ($tauxSuivi < 50)
            <div class="alert alert-performance">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Attention :</strong> Le taux de suivi des indicateurs est faible ({{ $tauxSuivi }}%).
                Il est recommandé d'intensifier le suivi pour une meilleure évaluation de la performance.
            </div>
        @endif

        <!-- Suivi détaillé par niveau -->
        @foreach ($programme->objectifsStrategiques as $objIndex => $objectif)
            <div class="card performance-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-bullseye"></i>
                        Objectif Stratégique {{ $objIndex + 1 }} : {{ $objectif->libelle }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Indicateurs objectif stratégique -->
                    @if ($objectif->indicateurs->count() > 0)
                        <div class="mb-4">
                            <h5 class="text-primary">📊 Indicateurs Objectif Stratégique</h5>
                            <div class="table-responsive">
                                <table class="table suivi-table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Unité</th>
                                            <th>Référence</th>
                                            <th>Cible</th>
                                            <th>Dernière Valeur</th>
                                            <th>Progression</th>
                                            <th>Statut</th>
                                            <th>Nombre Suivis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($objectif->indicateurs as $indicateur)
                                            @php
                                                $dernierSuivi = $indicateur->suivis->sortByDesc('date_suivi')->first();
                                                $valeurActuelle = $dernierSuivi
                                                    ? $dernierSuivi->valeur_actuelle
                                                    : $indicateur->valeur_ref;
                                                $progression = 0;

                                                if (
                                                    $indicateur->valeur_cible != $indicateur->valeur_ref &&
                                                    $indicateur->valeur_ref != 0
                                                ) {
                                                    $progression =
                                                        (($valeurActuelle - $indicateur->valeur_ref) /
                                                            ($indicateur->valeur_cible - $indicateur->valeur_ref)) *
                                                        100;
                                                    $progression = max(0, min(100, $progression));
                                                }

                                                $statut = 'Non suivi';
                                                $classeStatut = 'secondary';
                                                if ($dernierSuivi) {
                                                    if ($progression >= 80) {
                                                        $statut = 'Excellent';
                                                        $classeStatut = 'success';
                                                    } elseif ($progression >= 60) {
                                                        $statut = 'Satisfaisant';
                                                        $classeStatut = 'info';
                                                    } elseif ($progression >= 40) {
                                                        $statut = 'Moyen';
                                                        $classeStatut = 'warning';
                                                    } else {
                                                        $statut = 'Faible';
                                                        $classeStatut = 'danger';
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $indicateur->libelle }}</td>
                                                <td>{{ $indicateur->unite_mesure }}</td>
                                                <td>{{ $indicateur->valeur_ref }}</td>
                                                <td>{{ $indicateur->valeur_cible }}</td>
                                                <td>
                                                    <strong>{{ $valeurActuelle }}</strong>
                                                    @if ($dernierSuivi)
                                                        <br><small
                                                            class="text-muted">{{ $dernierSuivi->date_suivi }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="indicator-progress">
                                                        <div class="progress-fill" style="width: {{ $progression }}%">
                                                            {{ round($progression, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $classeStatut }} status-badge">{{ $statut }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-primary">{{ $indicateur->suivis->count() }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Actions et leurs indicateurs -->
                    @foreach ($objectif->actions as $actionIndex => $action)
                        <div class="card border-left-success mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0 text-success">
                                    <i class="fas fa-play"></i>
                                    Action {{ $objIndex + 1 }}.{{ $actionIndex + 1 }} : {{ $action->libelle }}
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                    <div class="mb-4">
                                        <h6 class="text-warning">
                                            <i class="fas fa-target"></i>
                                            Objectif Spécifique
                                            {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }} :
                                            {{ $objSpec->libelle }}
                                        </h6>

                                        <!-- Indicateurs objectif spécifique -->
                                        @if ($objSpec->indicateurs->count() > 0)
                                            <div class="table-responsive mb-3">
                                                <table class="table table-sm suivi-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Indicateur OSp</th>
                                                            <th>Référence</th>
                                                            <th>Cible</th>
                                                            <th>Actuelle</th>
                                                            <th>Progression</th>
                                                            <th>Suivis</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($objSpec->indicateurs as $indicateur)
                                                            @php
                                                                $dernierSuivi = $indicateur->suivis
                                                                    ->sortByDesc('date_suivi')
                                                                    ->first();
                                                                $valeurActuelle = $dernierSuivi
                                                                    ? $dernierSuivi->valeur_actuelle
                                                                    : $indicateur->valeur_ref;
                                                                $progression = 0;

                                                                if (
                                                                    $indicateur->valeur_cible !=
                                                                        $indicateur->valeur_ref &&
                                                                    $indicateur->valeur_ref != 0
                                                                ) {
                                                                    $progression =
                                                                        (($valeurActuelle - $indicateur->valeur_ref) /
                                                                            ($indicateur->valeur_cible -
                                                                                $indicateur->valeur_ref)) *
                                                                        100;
                                                                    $progression = max(0, min(100, $progression));
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $indicateur->libelle }}</td>
                                                                <td>{{ $indicateur->valeur_ref }}</td>
                                                                <td>{{ $indicateur->valeur_cible }}</td>
                                                                <td>{{ $valeurActuelle }}</td>
                                                                <td>
                                                                    <div class="indicator-progress"
                                                                        style="min-height: 15px;">
                                                                        <div class="progress-fill"
                                                                            style="width: {{ $progression }}%; font-size: 10px;">
                                                                            {{ round($progression, 1) }}%
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge badge-info badge-sm">{{ $indicateur->suivis->count() }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <!-- Activités et leurs indicateurs -->
                                        @foreach ($objSpec->activites as $activiteIndex => $activite)
                                            @if ($activite->indicateurs->count() > 0)
                                                <div class="ml-3 mb-3">
                                                    <h6 class="font-weight-bold">
                                                        ⚡ Activité
                                                        {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}.{{ $activiteIndex + 1 }}
                                                        : {{ $activite->libelle }}
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Indicateur Activité</th>
                                                                    <th>Unité</th>
                                                                    <th>Référence</th>
                                                                    <th>Cible</th>
                                                                    <th>Actuelle</th>
                                                                    <th>Progression</th>
                                                                    <th>Dernière MAJ</th>
                                                                    <th>Suivis</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($activite->indicateurs as $indicateur)
                                                                    @php
                                                                        $dernierSuivi = $indicateur->suivis
                                                                            ->sortByDesc('date_suivi')
                                                                            ->first();
                                                                        $valeurActuelle = $dernierSuivi
                                                                            ? $dernierSuivi->valeur_actuelle
                                                                            : $indicateur->valeur_ref;
                                                                        $progression = 0;

                                                                        if (
                                                                            $indicateur->valeur_cible !=
                                                                                $indicateur->valeur_ref &&
                                                                            $indicateur->valeur_ref != 0
                                                                        ) {
                                                                            $progression =
                                                                                (($valeurActuelle -
                                                                                    $indicateur->valeur_ref) /
                                                                                    ($indicateur->valeur_cible -
                                                                                        $indicateur->valeur_ref)) *
                                                                                100;
                                                                            $progression = max(
                                                                                0,
                                                                                min(100, $progression),
                                                                            );
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $indicateur->libelle }}</td>
                                                                        <td>{{ $indicateur->unite_mesure }}</td>
                                                                        <td>{{ $indicateur->valeur_ref }}</td>
                                                                        <td>{{ $indicateur->valeur_cible }}</td>
                                                                        <td>
                                                                            <strong
                                                                                class="text-primary">{{ $valeurActuelle }}</strong>
                                                                        </td>
                                                                        <td>
                                                                            <div class="indicator-progress"
                                                                                style="min-height: 15px;">
                                                                                <div class="progress-fill"
                                                                                    style="width: {{ $progression }}%; font-size: 10px;">
                                                                                    {{ round($progression, 1) }}%
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            @if ($dernierSuivi)
                                                                                <small
                                                                                    class="text-muted">{{ \Carbon\Carbon::parse($dernierSuivi->date_suivi)->format('d/m/Y') }}</small>
                                                                            @else
                                                                                <small class="text-danger">Aucun
                                                                                    suivi</small>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($indicateur->suivis->count() > 0)
                                                                                <span
                                                                                    class="badge badge-success badge-sm">{{ $indicateur->suivis->count() }}</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge badge-secondary badge-sm">0</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Graphiques de performance -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-chart-pie"></i> Analyse de Performance</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Graphique en secteurs du taux de suivi -->
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="suiviChart"></canvas>
                                </div>
                                <div class="text-center">
                                    <strong>Répartition des Indicateurs par Statut de Suivi</strong>
                                </div>
                            </div>

                            <!-- Graphique en barres des progressions -->
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="progressionChart"></canvas>
                                </div>
                                <div class="text-center">
                                    <strong>Progression par Niveau d'Objectif</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommandations -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-lightbulb"></i> Recommandations d'Amélioration</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            @if ($tauxSuivi < 30)
                                <li><i class="fas fa-exclamation-circle text-danger mr-2"></i>
                                    <strong>Urgent :</strong> Mettre en place un système de suivi régulier pour les
                                    indicateurs non suivis.
                                </li>
                            @endif

                            @if ($indicateursAvecSuivi > 0 && $totalSuivis / $indicateursAvecSuivi < 2)
                                <li><i class="fas fa-clock text-warning mr-2"></i>
                                    <strong>Fréquence :</strong> Augmenter la fréquence de collecte des données de suivi.
                                </li>
                            @endif

                            @if ($tauxSuivi >= 70)
                                <li><i class="fas fa-check-circle text-success mr-2"></i>
                                    <strong>Félicitations :</strong> Bon taux de suivi maintenu. Continuer l'effort de
                                    collecte régulière.
                                </li>
                            @endif

                            <li><i class="fas fa-chart-line text-info mr-2"></i>
                                <strong>Analyse :</strong> Effectuer une analyse trimestrielle des écarts entre cibles et
                                réalisations.
                            </li>

                            <li><i class="fas fa-users text-primary mr-2"></i>
                                <strong>Formation :</strong> Sensibiliser les équipes à l'importance du suivi des
                                indicateurs de performance.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts pour les graphiques -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Données pour le graphique de suivi
            const totalIndicateurs = {{ $totalIndicateurs }};
            const indicateursAvecSuivi = {{ $indicateursAvecSuivi }};
            const indicateursSansSuivi = totalIndicateurs - indicateursAvecSuivi;

            // Graphique en secteurs du taux de suivi
            const suiviCtx = document.getElementById('suiviChart').getContext('2d');
            new Chart(suiviCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Avec Suivi', 'Sans Suivi'],
                    datasets: [{
                        data: [indicateursAvecSuivi, indicateursSansSuivi],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label;
                                    const value = context.raw;
                                    const percentage = ((value / totalIndicateurs) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Données pour le graphique de progression par niveau
            @php
                $niveaux = ['Objectifs Stratégiques', 'Objectifs Spécifiques', 'Activités'];
                $progressions = [0, 0, 0]; // Moyennes par niveau
                $compteurs = [0, 0, 0];

                foreach ($programme->objectifsStrategiques as $objectif) {
                    foreach ($objectif->indicateurs as $indicateur) {
                        $dernierSuivi = $indicateur->suivis->sortByDesc('date_suivi')->first();
                        if ($dernierSuivi) {
                            $valeurActuelle = $dernierSuivi->valeur_actuelle;
                            if ($indicateur->valeur_cible != $indicateur->valeur_ref && $indicateur->valeur_ref != 0) {
                                $progression = (($valeurActuelle - $indicateur->valeur_ref) / ($indicateur->valeur_cible - $indicateur->valeur_ref)) * 100;
                                $progression = max(0, min(100, $progression));
                                $progressions[0] += $progression;
                                $compteurs[0]++;
                            }
                        }
                    }

                    foreach ($objectif->actions as $action) {
                        foreach ($action->objectifsSpecifiques as $objSpec) {
                            foreach ($objSpec->indicateurs as $indicateur) {
                                $dernierSuivi = $indicateur->suivis->sortByDesc('date_suivi')->first();
                                if ($dernierSuivi) {
                                    $valeurActuelle = $dernierSuivi->valeur_actuelle;
                                    if ($indicateur->valeur_cible != $indicateur->valeur_ref && $indicateur->valeur_ref != 0) {
                                        $progression = (($valeurActuelle - $indicateur->valeur_ref) / ($indicateur->valeur_cible - $indicateur->valeur_ref)) * 100;
                                        $progression = max(0, min(100, $progression));
                                        $progressions[1] += $progression;
                                        $compteurs[1]++;
                                    }
                                }
                            }

                            foreach ($objSpec->activites as $activite) {
                                foreach ($activite->indicateurs as $indicateur) {
                                    $dernierSuivi = $indicateur->suivis->sortByDesc('date_suivi')->first();
                                    if ($dernierSuivi) {
                                        $valeurActuelle = $dernierSuivi->valeur_actuelle;
                                        if ($indicateur->valeur_cible != $indicateur->valeur_ref && $indicateur->valeur_ref != 0) {
                                            $progression = (($valeurActuelle - $indicateur->valeur_ref) / ($indicateur->valeur_cible - $indicateur->valeur_ref)) * 100;
                                            $progression = max(0, min(100, $progression));
                                            $progressions[2] += $progression;
                                            $compteurs[2]++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Calculer les moyennes
                for ($i = 0; $i < 3; $i++) {
                    if ($compteurs[$i] > 0) {
                        $progressions[$i] = round($progressions[$i] / $compteurs[$i], 1);
                    }
                }
            @endphp

            // Graphique en barres des progressions
            const progressionCtx = document.getElementById('progressionChart').getContext('2d');
            new Chart(progressionCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($niveaux) !!},
                    datasets: [{
                        label: 'Progression Moyenne (%)',
                        data: {!! json_encode($progressions) !!},
                        backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                        borderColor: ['#0056b3', '#1e7e34', '#e0a800'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        });

        // Fonction d'export Excel
        function exportSuiviToExcel() {
            const wb = XLSX.utils.book_new();

            // Feuille de synthèse
            const synthese = [
                ['Synthèse du Suivi de Performance'],
                ['Programme', '{{ $programme->numero_programme }} : {{ $programme->libelle }}'],
                ['Date d\'export', new Date().toLocaleDateString()],
                [''],
                ['Indicateurs Totaux', {{ $totalIndicateurs }}],
                ['Indicateurs avec Suivi', {{ $indicateursAvecSuivi }}],
                ['Taux de Suivi', '{{ $tauxSuivi }}%'],
                ['Total Suivis', {{ $totalSuivis }}]
            ];

            const ws_synthese = XLSX.utils.aoa_to_sheet(synthese);
            XLSX.utils.book_append_sheet(wb, ws_synthese, "Synthèse");

            // Exporter les tableaux de suivi
            const tables = document.querySelectorAll('.suivi-table');
            tables.forEach((table, index) => {
                const ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, `Suivi_${index + 1}`);
            });

            XLSX.writeFile(wb,
                `Suivi_Performance_Programme_{{ $programme->numero_programme }}_${new Date().toISOString().slice(0,10)}.xlsx`
            );
        }
    </script>
@endsection
