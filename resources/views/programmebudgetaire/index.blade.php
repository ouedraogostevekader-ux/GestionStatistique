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

                                            <td>{{$programme->structure_id}}</td>
                                            <td>
                                                @php
                                                    $responsableActif = $programme->responsables()->get()->last();
                                                @endphp

                                                @if($responsableActif)
                                                    {{ $responsableActif->name }}
                                                    <br>
                                                    <small class="text-muted">
                                                        Depuis le
                                                        {{ \Carbon\Carbon::parse($responsableActif->pivot->date_debut)->format('d/m/Y') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Aucun responsable assigné</span>
                                                @endif
                                            </td>


                                            <td class="text-end">

                                                <div class="btn-group">

                                                    <!-- Voir toujours accessible -->
                                                    <a href="{{ route('programmebudgetaire.show', $programme->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a href="{{ $programme->isActive ? route('programmebudgetaire.edit', $programme->id) : '#' }}"
                                                        class="btn btn-primary btn-xs {{ !$programme->isActive ? 'locked-program' : '' }}"
                                                        title="{{ $programme->isActive ? 'Modifier' : 'Programme verrouillé' }}"
                                                        data-message="Ce programme est verrouillé et ne peut pas être modifié.">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>

                                                    <!-- Delete -->
                                                    <button type="button"
                                                        class="btn btn-outline-danger {{ !$programme->isActive ? 'locked-program' : 'delete-btn' }}"
                                                        title="{{ $programme->isActive ? 'Supprimer' : 'Programme verrouillé' }}"
                                                        data-url="{{ $programme->isActive ? route('programmebudgetaire.destroy', $programme->id) : '' }}"
                                                        data-message="Ce programme est verrouillé et ne peut pas être supprimé.">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>

                                                    <!-- Toggle isActive -->
                                                    <button type="button" class="btn btn-outline-warning"
                                                        onclick="event.preventDefault(); document.getElementById('toggle-form-{{ $programme->id }}').submit();"
                                                        title="{{ $programme->isActive ? 'Verrouiller ce programme' : 'Déverrouiller ce programme' }}">
                                                        <i class="fas {{ $programme->isActive ? 'bi-unlock' : 'bi-lock' }}"></i>
                                                    </button>

                                                    <form id="toggle-form-{{ $programme->id }}"
                                                        action="{{ route('programmebudgetaire.toggleEditable', $programme->id) }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                        @method('PATCH')
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