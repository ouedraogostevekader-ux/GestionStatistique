@extends('layouts.admin')
@section('breadcrumb', 'Dashboard Assignations')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">
                        <h2>Dashboard des Assignations : (<badge class="text-success">{{ $assignations->count() }}</badge>)
                        </h2>
                        <p class="text-muted">Vue d'ensemble des assignations de tableaux par structure</p>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>En attente</option>
                            <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>En cours</option>
                            <option value="2" {{ request('statut') == '2' ? 'selected' : '' }}>Terminé</option>
                            <option value="3" {{ request('statut') == '3' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="annee" class="form-label">Année</label>
                        <select name="annee" id="annee" class="form-select form-select-sm">
                            <option value="">Toutes les années</option>
                            @for ($i = date('Y'); $i >= date('Y') - 3; $i--)
                                <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="version_id" class="form-label">Version</label>
                        <select name="version_id" id="version_id" class="form-select form-select-sm">
                            <option value="">Toutes les versions</option>
                            @foreach ($versions as $version)
                                <option value="{{ $version->id }}"
                                    {{ request('version_id') == $version->id ? 'selected' : '' }}>
                                    {{ $version->code }}--{{ $version->annee }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div class="col-md-3">
                            <label for="structure_id" class="form-label">Structure</label>
                            <select name="structure_id" id="structure_id" class="form-select form-select-sm">
                                <option value="">Toutes les structures</option>
                                @foreach ($structures as $structure)
                                    <option value="{{ $structure->id }}"
                                        {{ request('structure_id') == $structure->id ? 'selected' : '' }}>
                                        {{ $structure->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Filtrer</button>
                        <a href="{{ route('assignations.index') }}"
                            class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- Liste des assignations -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Assignations récentes</h6>
                @can('assignation-create')
                    <a href="{{ route('tableaux.affectations') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Assigner
                    </a>
                @endcan
            </div>
            <div class="card-body">
                @if ($assignations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm" id="example2">
                            <thead>
                                <tr>
                                    <th>Tableau</th>
                                    <th>Structure</th>
                                    {{-- <th>Responsable</th> --}}
                                    <th>Statut</th>
                                    <th>Progrès</th>
                                    <th>Date début</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignations as $assignation)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($assignation->tableau->libelle, 30) }}</strong>
                                            <br><small
                                                class="text-muted">{{ $assignation->tableau->categorie->libelle }}</small>
                                        </td>
                                        <td>
                                            {{ $assignation->structure->nom }}
                                            <br><small class="text-muted">{{ $assignation->structure->sigle }}</small>
                                        </td>
                                        {{-- <td>{{ $assignation->responsable ?? '-' }}</td> --}}
                                        <td>
                                            <span class="badge bg-{{ $assignation->statut_class }}">
                                                {{ $assignation->statut_libelle }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $assignation->statut_class }}"
                                                    style="width: {{ $assignation->progress }}%"></div>
                                            </div>
                                            <small>{{ $assignation->progress }}%</small>
                                        </td>
                                        <td>
                                            @if ($assignation->date_debut)
                                                <small>{{ $assignation->date_debut }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('assignation-list')
                                                    <a href="{{ route('assignations.show', $assignation->id) }}"
                                                        class="btn btn-outline-info" title="Voir les détails de l'assignation">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endcan
                                                {{-- Modifier si l'assignation est en cours c'est a dire avant la date debut, ou est expirer c'est a dire apres la date fin --}}
                                                @if ($assignation->statut == 0 || $assignation->statut == 3)
                                                    @can('assignation-edit')
                                                        <a href="{{ route('assignations.edit', $assignation->id) }}"
                                                            class="btn btn-outline-warning" title="Modifier l'assignation">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('assignation-delete')
                                                        <form action="{{ route('assignations.destroy', $assignation->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Voulez-vous vraiment supprimer cette assignation ?')">
                                                            @csrf
                                                            @method ('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger"
                                                                title="suppression de l'assignation">
                                                                <i class="bi bi-archive"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                                @if ($assignation->statut == 1)
                                                    @can('soumission-create')
                                                        <a href="{{ route('tableaux.saisie', $assignation->tableau_id) }}"
                                                            class="btn btn-outline-success">
                                                            <i class="bi bi-edit"></i> Saisir Données
                                                        </a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard-x display-4 text-muted"></i>
                        <h6 class="text-muted mt-2">Aucune assignation trouvée</h6>
                        <p class="text-muted small">Aucune assignation ne correspond aux critères sélectionnés.</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- Statistiques globales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                <p class="mb-0 small">Total assignations</p>
                            </div>
                            <div class="ms-auto">
                                <i class="bi bi-clipboard-check display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['en_attente'] }}</h3>
                                <p class="mb-0 small">En attente</p>
                            </div>
                            <div class="ms-auto">
                                <i class="bi bi-clock display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['en_cours'] }}</h3>
                                <p class="mb-0 small">En cours</p>
                            </div>
                            <div class="ms-auto">
                                <i class="bi bi-play-circle display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['terminees'] }}</h3>
                                <p class="mb-0 small">Terminées</p>
                            </div>
                            <div class="ms-auto">
                                <i class="bi bi-check-circle display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de répartition -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Répartition par statut</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Assignations par mois</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Assignations en retard -->
        @if ($assignationsEnRetard->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Assignations en retard
                        ({{ $assignationsEnRetard->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tableau</th>
                                    <th>Structure</th>
                                    <th>Date limite dépassée</th>
                                    <th>Retard (jours)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignationsEnRetard as $assignation)
                                    <tr>
                                        <td>{{ Str::limit($assignation->tableau->libelle, 40) }}</td>
                                        <td>{{ $assignation->structure->nom }}</td>
                                        <td>{{ $assignation->date_fin }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($assignation->date_fin)) }}
                                                jours
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('assignations.edit', [$assignation->tableau_id, $assignation->id]) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Charts.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique des statuts
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'En cours', 'Terminé', 'Suspendu'],
                    datasets: [{
                        data: [{{ $stats['en_attente'] }}, {{ $stats['en_cours'] }},
                            {{ $stats['terminees'] }}, {{ $stats['suspendues'] }}
                        ],
                        backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Graphique mensuel (exemple avec données fictives - à adapter selon vos besoins)
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Assignations créées',
                        data: {!! json_encode($monthlyData ?? [5, 8, 12, 7, 15, 9]) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
