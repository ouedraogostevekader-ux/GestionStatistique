{{-- Informations principales de la version --}}
<div class="col-md-12 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 gap-2">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="bi bi-bookmark-check me-2"></i>{{ $version->libelle ?? 'Version ' . $version->code }}
            </h4>
            <span class="badge bg-secondary fs-6">Édition {{ $version->annee ?? 'N/A' }}</span>
        </div>
        <div class="d-flex gap-2">
            @can('annuaire-list')
                <a href="{{ route('annuaires.index', ['version' => $version->id]) }}" class="btn btn-success">
                    <i class="bi bi-book me-1"></i> Consulter l'annuaire
                </a>
            @endcan
            @can('version-edit')
                <a href="{{ route('versions.edit', $version->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </a>
            @endcan
        </div>
    </div>
</div>

<div class="col-md-4 mb-3">
    <div class="p-3 bg-light rounded border h-100">
        <label class="form-label text-muted small mb-1 fw-bold">Code de la version</label>
        <div class="fw-bold fs-6 text-dark">{{ $version->code }}</div>
    </div>
</div>

<div class="col-md-4 mb-3">
    <div class="p-3 bg-light rounded border h-100">
        <label class="form-label text-muted small mb-1 fw-bold">Libellé</label>
        <div class="fw-bold fs-6 text-dark">{{ $version->libelle ?? '-' }}</div>
    </div>
</div>

<div class="col-md-4 mb-3">
    <div class="p-3 bg-light rounded border h-100">
        <label class="form-label text-muted small mb-1 fw-bold">Année statistique</label>
        <div class="fw-bold fs-6 text-dark">{{ $version->annee ?? '-' }}</div>
    </div>
</div>

<div class="col-md-6 mb-3">
    <div class="p-3 bg-light rounded border h-100">
        <label class="form-label text-muted small mb-1 fw-bold">Date de création</label>
        <div class="fw-semibold text-dark">{{ $version->created_at ? $version->created_at->format('d/m/Y H:i') : '-' }}</div>
    </div>
</div>

<div class="col-md-6 mb-3">
    <div class="p-3 bg-light rounded border h-100">
        <label class="form-label text-muted small mb-1 fw-bold">Nombre de tableaux assignés</label>
        <div class="fw-bold text-info fs-6">{{ $version->assignations->count() }} tableau(x)</div>
    </div>
</div>

{{-- Liste des tableaux assignés --}}
<div class="col-12 mt-4">
    <div class="card border">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-secondary">
                <i class="bi bi-table me-2"></i>Tableaux associés à cette version ({{ $version->assignations->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if ($version->assignations->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Aucun tableau n'est assigné à cette version pour le moment.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Tableau</th>
                                <th>Catégorie</th>
                                <th>Structure responsable</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($version->assignations as $index => $assignation)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ $assignation->tableau->libelle ?? 'Tableau non spécifié' }}
                                    </td>
                                    <td>
                                        @if ($assignation->tableau && $assignation->tableau->categorie)
                                            <span class="badge bg-secondary">{{ $assignation->tableau->categorie->libelle }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $assignation->structure->sigle ?? ($assignation->structure->nom ?? '-') }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statut = $assignation->statut ?? 'Actif';
                                            $badgeClass = match(strtolower($statut)) {
                                                'valide', 'validé', 'terminé', 'termine' => 'bg-success',
                                                'en cours' => 'bg-primary',
                                                'en attente' => 'bg-warning text-dark',
                                                'suspendu' => 'bg-danger',
                                                default => 'bg-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statut }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
