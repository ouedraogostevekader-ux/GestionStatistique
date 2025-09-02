@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des Programmes Budgétaires</h5>
                    <a href="{{ route('programmebudgetaire.create') }}" class="btn btn-light btn-sm">
                        + Nouveau
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th>Numéro programme</th>
                                    <th>Programme activité</th>
                                    <th>Responsable Budgétaire</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programmes as $programme)
                                <tr>
                                    <td>{{ $programme->libelle }}</td>
                                    <td>{{ $programme->numero_programme }}</td>
                                    <td>{{ $programme->programme_activite_id }}</td>
                                    <td>{{ $programme->code }} </td>
                              <td class="text-end">
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('programmebudgetaire.show', $programme->id) }}" 
           class="btn btn-outline-info btn-sm" 
           title="Voir">
            <i class="bi bi-eye-fill"></i>
        </a>

        <a href="{{ route('programmebudgetaire.edit', $programme->id) }}" 
           class="btn btn-outline-warning btn-sm" 
           title="Modifier">
            <i class="bi bi-pencil-fill"></i>
        </a>

        <form action="{{ route('programmebudgetaire.destroy', $programme->id) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit" 
                    class="btn btn-outline-danger btn-sm" 
                    onclick="return confirm('Confirmer la suppression ?')" 
                    title="Supprimer">
                <i class="bi bi-trash-fill"></i>
            </button>
        </form>
    </div>
</td>


                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $programmes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
