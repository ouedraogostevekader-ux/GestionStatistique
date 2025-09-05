@extends('layouts.admin')
@section('breadcrumb', 'Programme budgétaire')
@section('content')
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des programmes budgétaires <span
                                class="btn btn-success">({{ $programmes->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-2 d-flex justify-content-end">
                        <a class="btn btn-primary" id="btnAjouter" href="{{ route('programmebudgetaire.create') }}">
                            <i class="bi bi-plus"></i> Ajouter
                        </a>
                        <a href="{{ route('programmes-budgetaires.create-duplicate') }}" id="btnEditer"
                            class="btn btn-warning text-white m-1">
                            <i class="bi bi-copy"></i> Crééer à partir d'un autre programme
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des Programmes Budgétaires</h5>
                        <a href="{{ route('programmebudgetaire.create') }}" class="btn btn-light btn-sm">
                            + Nouveau
                        </a>

                    </div>

                    <div class="card-body">
                        @if (session('success'))
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
                                    @foreach ($programmes as $programme)
                                        <tr>
                                            <td>{{ $programme->libelle }}</td>
                                            <td>{{ $programme->numero_programme }}</td>
                                            {{-- <td>{{ $programme->structure_id }}</td> --}}
                                            <td>
                                                @php
                                                    $responsableActif = $programme->responsables()->get()->last();
                                                @endphp

                                                @if ($responsableActif)
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

                                                    <a href="{{ route('programmebudgetaire.edit', $programme->id) }}"
                                                        class="btn btn-warning btn-xs" title="Modifier">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <a href="{{ route('programmebudgetaire.os', $programme->id) }}"
                                                        class="btn btn-success btn-xs"
                                                        title="Voir les objectifs stratégiques">
                                                        <i class="bi bi-bullseye"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                                        title="Supprimer"
                                                        data-url="{{ route('programmebudgetaire.destroy', $programme->id) }}"
                                                        data-message="Le programme <strong>{{ $programme->libelle }}</strong> sera supprimé. Continuer ?">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>


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
