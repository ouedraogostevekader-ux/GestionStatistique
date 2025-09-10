@extends('layouts.admin')
@section('breadcrumb', 'Tableaux/structure')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Structure du tableau: {{ $tableau->libelle }}</h2>
                <p class="text-muted">{{ $tableau->description }}</p>
            </div>
            <div>
                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> Visualiser
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Lignes du tableau</h5>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAjouterLigne">
                            <i class="fas fa-plus"></i> Ajouter Ligne
                        </button>
                    </div>
                    <div class="card-body">
                        @forelse($lignes as $ligne)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $ligne->ordre }}.</strong> {{ $ligne->nom_ligne }}
                                    @if ($ligne->parent_id)
                                        <small class="text-muted">(Sous-ligne)</small>
                                    @endif
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="supprimerLigne('{{ $ligne->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Aucune ligne définie</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Colonnes du tableau</h5>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAjouterColonne">
                            <i class="fas fa-plus"></i> Ajouter Colonne
                        </button>
                    </div>
                    <div class="card-body">
                        @forelse($colonnes as $colonne)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $colonne->ordre }}.</strong> {{ $colonne->colonne_ligne }}
                                    @if ($colonne->parent_id)
                                        <small class="text-muted">(Sous-colonne)</small>
                                    @endif
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="supprimerColonne('{{ $colonne->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Aucune colonne définie</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Ligne -->
    <div class="modal fade" id="modalAjouterLigne" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tableaux.add-ligne', $tableau->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une ligne</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_ligne" class="form-label">Nom de la ligne *</label>
                            <input type="text" class="form-control" id="nom_ligne" name="nom_ligne" required>
                        </div>
                        <div class="mb-3">
                            <label for="ordre" class="form-label">Ordre *</label>
                            <input type="number" class="form-control" id="ordre" name="ordre" required>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Ligne parente (optionnel)</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">Ligne principale</option>
                                @foreach ($lignes->whereNull('parent_id') as $ligne)
                                    <option value="{{ $ligne->id }}">{{ $ligne->nom_ligne }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Colonne -->
    <div class="modal fade" id="modalAjouterColonne" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tableaux.add-colonne', $tableau->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une colonne</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="colonne_ligne" class="form-label">Nom de la colonne *</label>
                            <input type="text" class="form-control" id="colonne_ligne" name="colonne_ligne" required>
                        </div>
                        <div class="mb-3">
                            <label for="ordre" class="form-label">Ordre *</label>
                            <input type="number" class="form-control" id="ordre" name="ordre" required>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Colonne parente (optionnel)</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">Colonne principale</option>
                                @foreach ($colonnes->whereNull('parent_id') as $colonne)
                                    <option value="{{ $colonne->id }}">{{ $colonne->colonne_ligne }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function supprimerLigne(ligneId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')) {
                // Implémentation de la suppression via AJAX
                fetch(`/admin/lignes/${ligneId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    }
                });
            }
        }

        function supprimerColonne(colonneId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette colonne ?')) {
                // Implémentation de la suppression via AJAX
                fetch(`/admin/colonnes/${colonneId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@endsection
