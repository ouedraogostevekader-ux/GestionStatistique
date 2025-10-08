@extends('layouts.admin')
@section('breadcrumb', 'Tableaux')
@section('content')
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-4">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Gestion des Tableaux <span
                            class="btn btn-info">({{ $tableaux->count() }})</span>
                    </li>
                </ol>
            </div>
            <div class="col-sm-8">
                @can('tableau-create')
                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('tableaux.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Tableau
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="example2">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Catégorie</th>
                                <th>Description</th>
                                <th>Créé le</th>
                                <th>Structure assignées</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tableaux as $tableau)
                                <tr>
                                    <td>{{ $tableau->libelle }}</td>
                                    <td>{{ $tableau->categorie->libelle ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($tableau->description, 50) }}</td>
                                    <td>{{ $tableau->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($tableau->assignations->count())
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($tableau->assignations as $assignation)
                                                    <li>
                                                        <span class="badge bg-secondary">
                                                            {{ $assignation->structure->nom ?? 'Structure inconnue' }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Aucune structure assignée</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group">
                                            @can('tableau-list')
                                                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-info btn-xs"
                                                    title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('tableau-edit')
                                                <a href="{{ route('tableaux.edit', $tableau->id) }}"
                                                    class="btn btn-primary btn-xs" title="Modifier">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan
                                            @can('tableau-delete')
                                                <button type="button" class="btn btn-outline-danger delete-btn"
                                                    title="Supprimer" data-url="{{ route('tableaux.destroy', $tableau->id) }}"
                                                    data-message="<strong>{{ $tableau->libelle }}</strong> sera supprimé. Continuer ?">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            @endcan
                                        </div>

                                        <a href="{{ route('tableaux.structure', $tableau->id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Structure">
                                            <i class="bi bi-table"></i>
                                        </a>

                                        <a href="{{ route('tableaux.saisie', $tableau->id) }}"
                                            class="btn btn-sm btn-outline-success" title="Saisir">
                                            <i class="bi bi-pen-fill"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
