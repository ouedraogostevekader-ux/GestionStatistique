@extends('layouts.admin')
@section('breadcrumb', 'Activités')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">
                            Liste des activités
                            <span class="badge bg-success ms-2">{{ $stats['total'] }}</span>
                        </li>
                    </ol>
                    <div class="text-muted small">
                        Budget total: {{ number_format($stats['budget_total'], 0, ',', ' ') }} FCFA
                        | Moyenne: {{ number_format($stats['moyenne_cout'], 0, ',', ' ') }} FCFA
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('activite-create')
                            <a class="btn btn-primary" id="btnAjouter" href="{{ route('activites.create') }}">
                                <i class="bi bi-plus"></i> Ajouter
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Section des filtres -->
            <div class="card mb-3" id="filters-section">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtres de recherche</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('activites.index') }}" id="filtersForm">
                        <div class="row g-3">
                            <!-- Filtre par année -->
                            <div class="col-md-2">
                                <label for="annee" class="form-label fw-bold">Année :</label>
                                <select name="annee" id="annee" class="form-select form-select-sm">
                                    @foreach ($annees as $year)
                                        <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par trimestre -->
                            <div class="col-md-2">
                                <label for="trimestre" class="form-label fw-bold">Trimestre :</label>
                                <select name="trimestre" id="trimestre" class="form-select form-select-sm">
                                    <option value="">-- Tous --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            {{ $trimestre == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par programme budgétaire -->
                            <div class="col-md-3">
                                <label for="programme_budgetaire_id" class="form-label fw-bold">Programme budgétaire
                                    :</label>
                                <select name="programme_budgetaire_id" id="programme_budgetaire_id"
                                    class="form-select form-select-sm">
                                    <option value="">-- Tous --</option>
                                    @foreach ($programmes as $programme)
                                        <option value="{{ $programme->id }}"
                                            {{ $programmeBudgetaireId == $programme->id ? 'selected' : '' }}>
                                            {{ $programme->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par structure -->
                            @if (Auth::user()->isAdmin())
                                <div class="col-md-3">
                                    <label for="structure_id" class="form-label fw-bold">Structure :</label>
                                    <select name="structure_id" id="structure_id" class="form-select form-select-sm">
                                        <option value="">-- Toutes --</option>
                                        @foreach ($structures as $structure)
                                            <option value="{{ $structure->id }}"
                                                {{ $structureId == $structure->id ? 'selected' : '' }}>
                                                {{ $structure->nom }} ({{ $structure->sigle }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Filtre par objectif spécifique -->
                            <div class="col-md-2">
                                <label for="objectif_specifique_id" class="form-label fw-bold">Objectif spécifique :</label>
                                <select name="objectif_specifique_id" id="objectif_specifique_id"
                                    class="form-select form-select-sm">
                                    <option value="">-- Tous --</option>
                                    @foreach ($objectifs as $objectif)
                                        <option value="{{ $objectif->id }}"
                                            {{ $objectifSpecifiqueId == $objectif->id ? 'selected' : '' }}>
                                            {{ Str::limit($objectif->libelle, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-10 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="bi bi-search"></i> Rechercher
                                </button>
                                <a href="{{ route('activites.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="card">
            <div class="card-body p-0">
                @if ($activites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="example1">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Activité</th>
                                    <th>Année</th>
                                    <th>Périodes</th>
                                    <th>Coût prévisionnel</th>
                                    <th>Objectif spécifique</th>
                                    <th>Indicateurs</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($activites as $key => $activite)
                                    <tr>
                                        <td class="text-center">{{ $activites->firstItem() + $key }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ Str::limit($activite->libelle, 320) }}</div>
                                            @if ($activite->objectifSpecifique && $activite->objectifSpecifique->action)
                                                <small class="text-muted">
                                                    {{ Str::limit($activite->objectifSpecifique->action->objectifStrategique->programmeBudgetaire->libelle ?? '', 40) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $activite->annee }}</span>
                                        </td>
                                        <td>
                                            @if ($activite->periodes->count() > 0)
                                                @foreach ($activite->periodes as $periode)
                                                    <span class="badge bg-secondary me-1">{{ $periode->libelle }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold text-success">
                                                {{ number_format($activite->cout_previsionnel, 0, ',', ' ') }}
                                            </span>
                                            <small class="text-muted d-block">FCFA</small>
                                        </td>
                                        <td>
                                            @if ($activite->objectifSpecifique)
                                                <div title="{{ $activite->objectifSpecifique->libelle }}">
                                                    {{ Str::limit($activite->objectifSpecifique->libelle, 50) }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $indicateursCount = $activite->indicateursProgrammes->count();
                                            @endphp
                                            @if ($indicateursCount > 0)
                                                <span class="badge bg-primary">{{ $indicateursCount }}</span>
                                            @else
                                                <span class="badge bg-warning">0</span>
                                            @endif
                                        </td>

                                        <td>
                                            <form action="{{ route('activites.destroy', $activite->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <div class='btn-group'>
                                                    @can('activite-list')
                                                        <a href="{{ route('activites.show', $activite->id) }}"
                                                            class="btn btn-info btn-xs" title="Voir"><i
                                                                class="bi bi-eye-fill"></i></a>
                                                    @endcan
                                                    @can('activite-edit')
                                                        <a href="{{ route('activites.edit', $activite->id) }}"
                                                            class="btn btn-warning btn-xs" id="btnEdit" title="Modifier"><i
                                                                class="bi bi-pencil-fill"></i></a>
                                                    @endcan
                                                    @can('activite-delete')
                                                        <button type="submit" class="btn btn-danger btn-xs"
                                                            onclick="return confirm('Supprimer cette activité ?')"
                                                            title="Supprimer">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $activites->firstItem() }} à {{ $activites->lastItem() }}
                            sur {{ $activites->total() }} activités
                        </div>
                        <div>
                            {{ $activites->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">Aucune activité trouvée</h4>
                        <p class="text-muted">
                            @if (request()->hasAny(['annee', 'trimestre', 'objectif_specifique_id', 'programme_budgetaire_id', 'structure_id']))
                                Aucune activité ne correspond à vos critères de recherche.
                            @else
                                Il n'y a pas encore d'activités enregistrées.
                            @endif
                        </p>
                        <a href="{{ route('activites.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Créer la première activité
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Script JavaScript externe -->
    <script src="{{ asset('js/activites-table.js') }}"></script>
    <script src="{{ asset('js/activites-hp-table.js') }}"></script>

    @if (request()->hasAny(['trimestre', 'objectif_specifique_id', 'programme_budgetaire_id', 'structure_id']))
        <script>
            // Afficher automatiquement les filtres s'il y a des paramètres actifs
            document.addEventListener('DOMContentLoaded', function() {
                const filtersSection = document.getElementById('filters-section');
                if (filtersSection) {
                    filtersSection.style.display = 'block';
                    const toggleBtn = document.querySelector('#toggleFilters i');
                    if (toggleBtn) {
                        toggleBtn.className = 'bi bi-funnel-fill';
                    }
                }
            });
        </script>
    @endif
@endsection
