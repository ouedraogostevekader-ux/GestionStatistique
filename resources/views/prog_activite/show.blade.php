@extends('layouts.admin')

{{-- @section('title', 'Programme ' . $programme->numero_programme) --}}

@section('content')
    <div class="container-fluid">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                        </h1>
                        @if ($programme->programmeActivite)
                            <p class="mb-0">Programme d'activité : {{ $programme->programmeActivite->libelle }}
                                ({{ $programme->programmeActivite->annee }})</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="btn-group w-100">
                    <a href="{{ route('programme-activites.tableau', $programme->id) }}" class="btn btn-success">
                        <i class="fas fa-table"></i> Vue Tableau
                    </a>
                    <a href="{{ route('programme-activites.suivi', $programme->id) }}" class="btn btn-warning">
                        <i class="fas fa-chart-line"></i> Suivi Performance
                    </a>
                    <button class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenu hiérarchique -->
        @foreach ($programme->objectifsStrategiques as $objectifIndex => $objectif)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-bullseye"></i>
                        Objectif Stratégique {{ $objectif->libelle }}
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Indicateurs de l'objectif stratégique -->
                    @if ($objectif->indicateurs->count() > 0)
                        <div class="mb-4">
                            <h5><i class="fas fa-chart-bar text-info"></i> Indicateurs</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Unité</th>
                                            <th>Référence</th>
                                            <th>Cible</th>
                                            <th>Suivi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($objectif->indicateurs as $indicateur)
                                            <tr>
                                                <td>{{ $indicateur->libelle }}</td>
                                                <td>{{ $indicateur->unite_mesure }}</td>
                                                <td>{{ $indicateur->valeur_ref }}</td>
                                                <td>{{ $indicateur->valeur_cible }}</td>
                                                <td>
                                                    @if ($indicateur->suivis->count() > 0)
                                                        <span
                                                            class="badge badge-success">{{ $indicateur->suivis->count() }}
                                                            suivis</span>
                                                    @else
                                                        <span class="badge badge-secondary">Aucun suivi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    @foreach ($objectif->actions as $actionIndex => $action)
                        <div class="card border-left-success mb-3">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">
                                    <i class="fas fa-play text-success"></i>
                                    Action {{ $action->libelle }}
                                </h4>
                            </div>
                            <div class="card-body">
                                <!-- Objectifs spécifiques -->
                                @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                    <div class="border rounded p-3 mb-3 bg-light">
                                        <h5>
                                            <i class="fas fa-target text-warning"></i>
                                            Objectif Spécifique
                                            {{ $objSpec->libelle }}
                                        </h5>

                                        <!-- Indicateurs objectif spécifique -->
                                        @if ($objSpec->indicateurs->count() > 0)
                                            <div class="mb-3">
                                                <h6>📊 Indicateurs</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Indicateur</th>
                                                                <th>Unité</th>
                                                                <th>Référence</th>
                                                                <th>Cible</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($objSpec->indicateurs as $indicateur)
                                                                <tr>
                                                                    <td>{{ $indicateur->libelle }}</td>
                                                                    <td>{{ $indicateur->unite_mesure }}</td>
                                                                    <td>{{ $indicateur->valeur_ref }}</td>
                                                                    <td>{{ $indicateur->valeur_cible }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Activités -->
                                        <div class="activites">
                                            <h6>⚡ Activités</h6>
                                            @foreach ($objSpec->activites as $activiteIndex => $activite)
                                                <div class="card border-warning mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <strong>
                                                                    Activité
                                                                </strong>
                                                                {{ $activite->libelle }}
                                                            </div>
                                                            <div class="col-md-4 text-right">
                                                                <span class="badge badge-info">
                                                                    {{ number_format($activite->cout_previsionnel) }} FCFA
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Périodes -->
                                                        <div class="mt-2">
                                                            <small><strong>Périodes :</strong></small>
                                                            @forelse($activite->periodes as $periode)
                                                                <span
                                                                    class="badge badge-secondary">{{ $periode->libelle }}</span>
                                                            @empty
                                                                <span class="text-muted">Non définies</span>
                                                            @endforelse
                                                        </div>

                                                        <!-- Indicateurs activité -->
                                                        @if ($activite->indicateurs->count() > 0)
                                                            <div class="mt-2">
                                                                <small><strong>Indicateurs :</strong></small>
                                                                @foreach ($activite->indicateurs as $indicateur)
                                                                    <div class="small text-muted">
                                                                        • {{ $indicateur->libelle }}
                                                                        ({{ $indicateur->unite_mesure }})
                                                                        :
                                                                        {{ $indicateur->valeur_ref }} →
                                                                        {{ $indicateur->valeur_cible }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('styles')
    <style>
        @media print {

            .btn,
            .card-header,
            .card-footer,
            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
@endsection
