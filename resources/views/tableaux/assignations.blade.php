@extends('layouts.admin')
@section('breadcrumb', 'Tableaux/Assignations')
@section('content')
    <div class="container-fluid">
        <!-- En-tête avec informations du tableau -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-table display-4 text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $tableau->libelle }}</h2>
                        <p class="text-muted mb-1">{{ $tableau->description }}</p>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info me-2">{{ $tableau->categorie->libelle }}</span>
                            <small class="text-muted">
                                Créé le {{ $tableau->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour au tableau
                    </a>
                    <a href="{{ route('tableaux.structure', $tableau->id) }}" class="btn btn-outline-info">
                        <i class="bi bi-gear"></i> Structure
                    </a>
                    <a href="{{ route('assignations.create', $tableau->id) }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Nouvelle assignation
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides en cartes -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-primary">
                                    <h4 class="mb-1 font-weight-bold">{{ $assignations->total() }}</h4>
                                    <p class="mb-0 text-muted">Total assignations</p>
                                </div>
                            </div>
                            <div class="text-primary">
                                <i class="bi bi-clipboard-check display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-warning">
                                    <h4 class="mb-1 font-weight-bold">{{ $assignations->where('statut', 0)->count() }}</h4>
                                    <p class="mb-0 text-muted">En attente</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="bi bi-clock display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-info">
                                    <h4 class="mb-1 font-weight-bold">{{ $assignations->where('statut', 1)->count() }}</h4>
                                    <p class="mb-0 text-muted">En cours</p>
                                </div>
                            </div>
                            <div class="text-info">
                                <i class="bi bi-play-circle display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-success">
                                    <h4 class="mb-1 font-weight-bold">{{ $assignations->where('statut', 2)->count() }}</h4>
                                    <p class="mb-0 text-muted">Terminées</p>
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-check-circle display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filtres
                    </h6>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filtersCollapse" aria-expanded="false">
                        <i class="bi bi-chevron-down"></i> Afficher/Masquer
                    </button>
                </div>
            </div>
            <div class="collapse" id="filtersCollapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('assignations.index', $tableau->id) }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select name="statut" id="statut" class="form-select form-select-sm">
                                    <option value="">Tous les statuts</option>
                                    <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>En attente
                                    </option>
                                    <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>En cours
                                    </option>
                                    <option value="2" {{ request('statut') == '2' ? 'selected' : '' }}>Terminé
                                    </option>
                                    <option value="3" {{ request('statut') == '3' ? 'selected' : '' }}>Suspendu
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="structure" class="form-label">Structure</label>
                                <select name="structure_id" id="structure" class="form-select form-select-sm">
                                    <option value="">Toutes les structures</option>
                                    @foreach ($structures as $structure)
                                        <option value="{{ $structure->id }}"
                                            {{ request('structure_id') == $structure->id ? 'selected' : '' }}>
                                            {{ $structure->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="annee" class="form-label">Année</label>
                                <select name="annee" id="annee" class="form-select form-select-sm">
                                    <option value="">Toutes</option>
                                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}"
                                            {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="periode" class="form-label">Période</label>
                                <select name="periode" id="periode" class="form-select form-select-sm">
                                    <option value="">Toutes</option>
                                    <option value="T1" {{ request('periode') == 'T1' ? 'selected' : '' }}>T1</option>
                                    <option value="T2" {{ request('periode') == 'T2' ? 'selected' : '' }}>T2</option>
                                    <option value="T3" {{ request('periode') == 'T3' ? 'selected' : '' }}>T3</option>
                                    <option value="T4" {{ request('periode') == 'T4' ? 'selected' : '' }}>T4</option>
                                    <option value="S1" {{ request('periode') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ request('periode') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="ANNUEL" {{ request('periode') == 'ANNUEL' ? 'selected' : '' }}>Annuel
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search"></i> Filtrer
                                    </button>
                                    <a href="{{ route('assignations.index', $tableau->id) }}"
                                        class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau principal -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Liste des assignations</h6>
                    <div class="d-flex align-items-center">
                        <small class="text-muted me-3">{{ $assignations->total() }} assignation(s)</small>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportData('csv')">
                                        <i class="bi bi-file-earmark-csv me-2"></i>Exporter CSV
                                    </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportData('excel')">
                                        <i class="bi bi-file-earmark-excel me-2"></i>Exporter Excel
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="printTable()">
                                        <i class="bi bi-printer me-2"></i>Imprimer
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if ($assignations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="assignationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Structure</th>
                                    <th>Responsable</th>
                                    <th style="width: 120px;">Statut</th>
                                    <th style="width: 100px;">Période</th>
                                    <th style="width: 80px;">Année</th>
                                    <th style="width: 100px;">Version</th>
                                    <th style="width: 120px;">Date début</th>
                                    <th style="width: 120px;">Date fin</th>
                                    <th style="width: 100px;">Progrès</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignations as $assignation)
                                    <tr data-assignation-id="{{ $assignation->id }}">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    value="{{ $assignation->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-building text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $assignation->structure->nom }}</div>
                                                    <small class="text-muted">{{ $assignation->structure->sigle }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($assignation->responsable)
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle me-1 text-muted"></i>
                                                    {{ $assignation->responsable }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm status-select"
                                                data-assignation-id="{{ $assignation->id }}"
                                                data-tableau-id="{{ $tableau->id }}">
                                                <option value="0" {{ $assignation->statut == 0 ? 'selected' : '' }}>
                                                    En attente
                                                </option>
                                                <option value="1" {{ $assignation->statut == 1 ? 'selected' : '' }}>
                                                    En cours
                                                </option>
                                                <option value="2" {{ $assignation->statut == 2 ? 'selected' : '' }}>
                                                    Terminé
                                                </option>
                                                <option value="3" {{ $assignation->statut == 3 ? 'selected' : '' }}>
                                                    Suspendu
                                                </option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            @if ($assignation->periode)
                                                <span class="badge bg-secondary">{{ $assignation->periode }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($assignation->annee)
                                                <span class="badge bg-info">{{ $assignation->annee }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($assignation->version)
                                                <small
                                                    class="badge bg-light text-dark border">{{ $assignation->version }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($assignation->date_debut)
                                                <small>{{ $assignation->date_debut->format('d/m/Y') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($assignation->date_fin)
                                                <small>{{ $assignation->date_fin }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $assignation->statut_class }}"
                                                    style="width: {{ $assignation->progress }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $assignation->progress }}%</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('assignations.edit', [$tableau->id, $assignation->id]) }}"
                                                    class="btn btn-outline-warning" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('tableaux.saisie', $tableau->id) }}"
                                                    class="btn btn-outline-success" title="Saisir données">
                                                    <i class="bi bi-table"></i>
                                                </a>
                                                <form
                                                    action="{{ route('assignations.destroy', [$tableau->id, $assignation->id]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette assignation ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination et informations -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $assignations->firstItem() }} à {{ $assignations->lastItem() }}
                                sur {{ $assignations->total() }} assignations
                            </div>
                            <div>
                                {{ $assignations->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-clipboard-x display-1 text-muted"></i>
                        </div>
                        <h4 class="text-muted">Aucune assignation trouvée</h4>
                        <p class="text-muted mb-4">
                            @if (request()->hasAny(['statut', 'structure_id', 'annee', 'periode']))
                                Aucune assignation ne correspond à vos critères de recherche.
                                <br><a href="{{ route('assignations.index', $tableau->id) }}"
                                    class="text-primary">Réinitialiser les filtres</a>
                            @else
                                Ce tableau n'a encore été assigné à aucune structure.
                            @endif
                        </p>
                        <a href="{{ route('assignations.create', $tableau->id) }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Créer la première assignation
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions de groupe -->
        @if ($assignations->count() > 0)
            <div class="card mt-3" id="groupActions" style="display: none;">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">
                            <span id="selectedCount">0</span> assignation(s) sélectionnée(s)
                        </span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-info" onclick="changeSelectedStatus(1)">
                                <i class="bi bi-play-circle me-1"></i>Marquer en cours
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="changeSelectedStatus(2)">
                                <i class="bi bi-check-circle me-1"></i>Marquer terminé
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="changeSelectedStatus(3)">
                                <i class="bi bi-pause-circle me-1"></i>Suspendre
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteSelected()">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de confirmation pour actions de groupe -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de la sélection multiple
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const groupActions = document.getElementById('groupActions');
            const selectedCount = document.getElementById('selectedCount');

            selectAllCheckbox?.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateGroupActions();
            });

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateGroupActions);
            });

            function updateGroupActions() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const count = checkedBoxes.length;

                if (selectedCount) selectedCount.textContent = count;

                if (groupActions) {
                    groupActions.style.display = count > 0 ? 'block' : 'none';
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = count === rowCheckboxes.length;
                    selectAllCheckbox.indeterminate = count > 0 && count < rowCheckboxes.length;
                }
            }

            // Changement de statut individuel via AJAX
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const assignationId = this.dataset.assignationId;
                    const tableauId = this.dataset.tableauId;
                    const newStatus = this.value;

                    this.disabled = true;

                    fetch(`/tableaux/${tableauId}/assignations/${assignationId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                statut: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('success', data.message);
                                // Recharger après 1 seconde pour mettre à jour les statistiques
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showAlert('danger', data.message);
                                this.selectedIndex = 0;
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            showAlert('danger', 'Une erreur est survenue');
                            this.selectedIndex = 0;
                        })
                        .finally(() => {
                            this.disabled = false;
                        });
                });
            });

            // Actions de groupe
            window.changeSelectedStatus = function(status) {
                const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (checkedIds.length === 0) return;

                const statusLabels = {
                    0: 'En attente',
                    1: 'En cours',
                    2: 'Terminé',
                    3: 'Suspendu'
                };
                const message =
                    `Voulez-vous changer le statut de ${checkedIds.length} assignation(s) à "${statusLabels[status]}" ?`;

                showConfirm(message, () => {
                    // Implémenter l'action de groupe ici
                    console.log('Changer statut:', checkedIds, 'vers', status);
                });
            };

            window.deleteSelected = function() {
                const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (checkedIds.length === 0) return;

                const message =
                    `Voulez-vous vraiment supprimer ${checkedIds.length} assignation(s) ? Cette action est irréversible.`;

                showConfirm(message, () => {
                    // Implémenter la suppression de groupe ici
                    console.log('Supprimer:', checkedIds);
                });
            };

            // Export et impression
            window.exportData = function(format) {
                console.log('Export en format:', format);
                // Implémenter l'export selon le format
            };

            window.printTable = function() {
                window.print();
            };

            // Fonctions utilitaires
            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.body.appendChild(alertDiv);
                setTimeout(() => alertDiv.remove(), 5000);
            }

            function showConfirm(message, callback) {
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('confirmMessage').textContent = message;

                const confirmBtn = document.getElementById('confirmAction');
                confirmBtn.onclick = () => {
                    callback();
                    modal.hide();
                };

                modal.show();
            }
        });
    </script>

    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .progress {
            background-color: #e9ecef;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa !important;
        }

        .table td {
            vertical-align: middle;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .status-select {
            border: none;
            background: transparent;
            font-size: 0.875rem;
        }

        .status-select:focus {
            border: 1px solid #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endsection
