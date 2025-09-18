@extends('layouts.admin')
@section('breadcrumb', 'Tableaux/categories')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Catégories</h2>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Catégorie
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Nombre de Tableaux</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $categorie)
                                <tr>
                                    <td>{{ $categorie->libelle }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $categorie->tableaux_count }}</span>
                                    </td>
                                    <td>{{ $categorie->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('categories.show', $categorie->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $categorie->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            @if ($categorie->tableaux_count == 0)
                                                <form action="{{ route('categories.destroy', $categorie->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucune catégorie trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
