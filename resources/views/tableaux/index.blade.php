@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Tableaux</h2>
        <a href="{{ route('tableaux.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Tableau
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                        @forelse($tableaux as $tableau)
                        <tr>
                            <td>{{ $tableau->libelle }}</td>
                            <td>{{ $tableau->categorie->libelle ?? 'N/A' }}</td>
                            <td>{{ Str::limit($tableau->description, 50) }}</td>
                            <td>{{ $tableau->created_at->format('d/m/Y') }}</td>
                            <td>
    @if($tableau->assignations->count())
        <ul class="list-unstyled mb-0">
            @foreach($tableau->assignations as $assignation)
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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tableaux.show', $tableau->id) }}" 
                                      class="btn btn-info btn-xs" title="Voir">
                                                                <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('tableaux.structure', $tableau->id) }}" 
                                       class="btn btn-group btn-xs" title="Edit">
                                        <i class="bi bi-pencil-fill "></i>
                                    </a>
                                    <a href="{{ route('tableaux.saisie', $tableau->id) }}" 
                                       class="btn btn-primary" title="Soumettre données">
                                        <i class="bi bi-file"></i>
                                    </a>
                                    <a href="{{ route('tableaux.edit', $tableau->id) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

             
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun tableau trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $tableaux->links() }}
        </div>
    </div>
</div>
@endsection