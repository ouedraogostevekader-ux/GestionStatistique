@extends('layouts.admin')
@section('breadcrumb', 'Tableaux/structure')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>{{ $tableau->libelle }}</h2>
            </div>
            <div class="d-flex justify-align-between">
                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-outline-secondary">
                    <i class="bi  bi-arrow-bar-left"></i> Retour
                </a>
                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> Visualiser
                </a>
            </div>
        </div>

        <div class="row">
            <div class="card-header">
                <div class="row">
                    <div class="col text-start">
                        @can('ligne_colonne-create')
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAjouterLigne">
                                <i class="fas fa-plus"></i> Ajouter Ligne
                            </button>
                        @endcan
                    </div>
                    <div class="col text-end">
                        @can('colonne_tableau-create')
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAjouterColonne">
                                <i class="fas fa-plus"></i> Ajouter Colonne
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @foreach ($lignes as $ligne)
                                @if ($ligne->ordre == 1)
                                    <th>{{ $ligne->nom_ligne }}
                                        <div class="btn btn-group">
                                            @can('ligne_tableau-delete')
                                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLigneModal-{{ $ligne->id ?? '' }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            @endcan
                                            @can('ligne_tableau-edit')
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editLigneModal-{{ $ligne->id ?? '' }}">
                                                    <i class="bi bi-pen"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </th>
                                @endif
                            @endforeach
                            @foreach ($colonnes as $colonne)
                                <th>{{ $colonne->colonne_ligne }}
                                    <div class="btn btn-group">
                                        @can('colonne_tableau-delete')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteColonneModal-{{ $colonne->id ?? '' }}">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        @endcan
                                        @can('colonne_tableau-edit')
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#editColonneModal-{{ $colonne->id ?? '' }}">
                                                <i class="bi bi-pen"></i>
                                            </button>
                                        @endcan
                                    </div>

                                </th>
                                <!-- Modal Delete Colonne-->
                                <div class="modal fade" id="deleteColonneModal-{{ $colonne->id ?? '' }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('tableaux.destroy-colonne', $colonne->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Confirmation de suppression</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p class="mb-0">
                                                        Voulez-vous vraiment supprimer cet élément ?
                                                        <br>
                                                        <strong class="text-danger">Cette action est irréversible.</strong>
                                                    </p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-danger">
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal update Colonne-->
                                <div class="modal fade" id="editColonneModal-{{ $colonne->id ?? '' }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('tableaux.update-colonne', $colonne->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Modifier</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Libellé</label>
                                                        <input type="text" name="colonne_ligne" class="form-control"
                                                            value="{{ $colonne->colonne_ligne }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Ordre</label>
                                                        <input type="number" name="ordre" value="{{ $colonne->ordre }}"
                                                            class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-warning">
                                                        Enregistrer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lignes as $ligne)
                            @if ($ligne->ordre !== 1)
                                <tr>
                                    <td>
                                        <strong>{{ $ligne->ordre }}.</strong> {{ $ligne->nom_ligne }}
                                        @if ($ligne->parent_id)
                                            <small class="text-muted">(Sous-ligne)</small>
                                        @endif
                                        <div class="btn btn-group">
                                            @can('ligne_tableau-delete')
                                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLigneModal-{{ $ligne->id ?? '' }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            @endcan
                                            @can('ligne_tableau-edit')
                                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editLigneModal-{{ $ligne->id ?? '' }}">
                                                    <i class="bi bi-pen"></i>
                                                </button>
                                            @endcan
                                        </div>

                                    </td>
                                    @foreach ($colonnes as $colonne)
                                        <td>--</td>
                                    @endforeach
                                </tr>
                            @endif
                            <!-- Modal Delete Ligne-->
                            <div class="modal fade" id="deleteLigneModal-{{ $ligne->id ?? '' }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('tableaux.destroy-ligne', $ligne->id) }}">
                                            @csrf
                                            @method('DELETE')

                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirmation de suppression</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <p class="mb-0">
                                                    Voulez-vous vraiment supprimer cet élément ?
                                                    <br>
                                                    <strong class="text-danger">Cette action est
                                                        irréversible.</strong>
                                                </p>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Annuler
                                                </button>
                                                <button type="submit" class="btn btn-danger">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal update Ligne-->
                            <div class="modal fade" id="editLigneModal-{{ $ligne->id ?? '' }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('tableaux.update-ligne', $ligne->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Modifier</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Libellé</label>
                                                    <input type="text" name="nom_ligne"
                                                        value="{{ $ligne->nom_ligne }}" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Ordre</label>
                                                    <input type="number" name="ordre" value="{{ $ligne->ordre }}"
                                                        class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Annuler
                                                </button>
                                                <button type="submit" class="btn btn-warning">
                                                    Enregistrer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-muted">{{ $tableau->description }}</p>
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
