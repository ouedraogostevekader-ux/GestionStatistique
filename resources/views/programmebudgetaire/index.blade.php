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
                        {{-- <a class="btn btn-primary" id="btnAjouter" href="{{ route('programmebudgetaire.create') }}">
                            <i class="bi bi-plus"></i> Ajouter
                        </a> --}}
                        <a href="{{ route('programmes-budgetaires.create-duplicate') }}" id="btnEditer"
                            class="btn btn-warning text-white m-1">
                            <i class="bi bi-copy"></i> Créer à partir d'un autre programme
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des Programmes Budgétaires</h5>
                        <a href="{{ route('programmebudgetaire.create') }}" id="nouveauPB" class="btn btn-success btn-sm">
                            + Nouveau
                        </a>

                    </div>
                    <form method="GET" action="{{ route('programmebudgetaire.index') }}" class="mb-3 row g-2">
                        <div class="col-md-3">
                            <label for="annee" class="form-label">Année</label>
                            <input type="number" name="annee" id="annee" class="form-control"
                                value="{{ request('annee') }}" placeholder="Ex: 2025">
                        </div>
                        <div class="col-md-3">
                            <label for="structure_id" class="form-label">Structure</label>
                            <select name="structure_id" id="structure_id" class="form-select">
                                <option value="">Toutes</option>
                                @foreach ($structures as $structure)
                                    <option value="{{ $structure->id }}"
                                        {{ request('structure_id') == $structure->id ? 'selected' : '' }}>
                                        {{ $structure->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="responsable_id" class="form-label">Responsable</label>
                            <select name="responsable_id" id="responsable_id" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('responsable_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                            <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-secondary">Réinitialiser</a>
                        </div>
                    </form>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="example2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Programme budgétaire</th>
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
                                                <td>
                                                    @if ($programme->isActive)
                                                        <span class="badge bg-success">Ouvert</span>
                                                    @else
                                                        <span class="badge bg-secondary">Verrouillé</span>
                                                    @endif
                                                </td>


                                                <td class="text-end">

                                                    <div class="btn-group">

                                                        <!-- Voir toujours accessible -->
                                                        @can('programme_Budgetaire-list')
                                                            <a href="{{ route('programmebudgetaire.show', $programme->id) }}"
                                                                class="btn btn-info btn-xs" title="Voir">
                                                                <i class="bi bi-eye-fill"></i>
                                                            </a>
                                                        @endcan

                                                        <!-- Edit -->
                                                        @can('programme_Budgetaire-edit')
                                                            <a href="{{ $programme->isActive ? route('programmebudgetaire.edit', $programme->id) : '#' }}"
                                                                class="btn btn-warning btn-xs {{ !$programme->isActive ? 'locked-program' : '' }}"
                                                                title="{{ $programme->isActive ? 'Modifier' : 'Programme verrouillé' }}"
                                                                data-message="Ce programme est verrouillé et ne peut pas être modifié.">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </a>
                                                        @endcan

                                                        <!-- Delete -->
                                                        @can('programme_Bugetaire-delete')
                                                            <button type="button"
                                                                class="btn btn-outline-danger {{ !$programme->isActive ? 'locked-program' : 'delete-btn' }}"
                                                                title="{{ $programme->isActive ? 'Supprimer' : 'Programme verrouillé' }}"
                                                                data-url="{{ $programme->isActive ? route('programmebudgetaire.destroy', $programme->id) : '' }}"
                                                                data-message="Ce programme est verrouillé et ne peut pas être supprimé.">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        @endcan

                                                        <!-- Toggle isActive -->

                                                        <button type="button"
                                                            class="btn btn-outline-warning toggle-program-btn"
                                                            data-id="{{ $programme->id }}"
                                                            data-libelle="{{ $programme->libelle }}"
                                                            data-active="{{ $programme->isActive }}"
                                                            title="{{ $programme->isActive ? 'Verrouiller ce programme' : 'Déverrouiller ce programme' }}">
                                                            <i
                                                                class="fas {{ $programme->isActive ? 'bi-unlock' : 'bi-lock' }}"></i>
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

                            {{-- <div class="mt-3">
                                {{ $programmes->links() }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="lockModal" tabindex="-1" aria-labelledby="lockModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="lockModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body" id="lockModalMessage">
                        <!-- Message dynamique -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-warning" id="confirmLockBtn">Confirmer</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection
