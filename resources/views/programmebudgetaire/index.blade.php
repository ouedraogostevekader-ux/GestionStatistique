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
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

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
<button type="button" 
        class="btn btn-outline-warning toggle-program-btn"
        data-id="{{ $programme->id }}"
        data-libelle="{{ $programme->libelle }}"
        data-active="{{ $programme->isActive }}"
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
<script src="{{ asset('dist/js/lock-programme.js') }}"></script>
@endsection
