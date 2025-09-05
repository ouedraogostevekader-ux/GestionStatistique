@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des Programmes Budgétaires</h5>
                        <a href="{{ route('programmebudgetaire.create') }}" class="btn btn-light btn-sm">
                            + Nouveau
                        </a>
                        <a href="{{ route('programmes-budgetaires.create-duplicate') }}" class="btn btn-warning text-white">
                            <i class="bi bi-copy"></i> Crééer à partir d'un autre programme
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
                                        {{-- <th>Programme activité</th> --}}
                                        <th>Responsable Budgétaire</th>
                                        <th>Etat</th>
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

                                            <td
                                                class="badge bg-{{ $programme->isActive ? 'success' : 'secondary' }} text-white">
                                                {{ $programme->isActive ? 'Ouvert' : 'Vérouillé' }}
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('programmebudgetaire.show', $programme->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>

                                                 
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
