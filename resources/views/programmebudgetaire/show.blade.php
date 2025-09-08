@extends('layouts.admin')
@section('breadcrumb', 'Programme budgétaire')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Détails du Programme Budgétaire</h5>
                    </div>

                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Libellé :</strong> {{ $programmeBudgetaire->libelle }}
                            </li>
                            <li class="list-group-item">
                                <strong>Code :</strong> {{ $programmeBudgetaire->numero_programme }}
                            </li>
                            <li class="list-group-item">
                                <strong>Structure</strong>
                                {{ $programmeBudgetaire->structures->isEmpty() ? 'Aucune structure associée' : '' }}
                                @foreach ($programmeBudgetaire->structures as $structure)
                                    <span class="badge bg-secondary">{{ $structure->nom }}</span>
                                @endforeach
                            </li>


                            <li class="list-group-item">
                                <strong>Responsable actuel :</strong>
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
                            </li>
                        </ul>
                        <li class="list-group-item">
                            <strong>Etat :</strong> <span
                                class="btn btn-{{ $programmeBudgetaire->isActive ? 'success' : 'secondary' }}">{{ $programmeBudgetaire->isActive ? 'Ouvert' : 'Vérouillé' }}</span>
                        </li>
                        <li class="list-group-item mt-3">
                            <strong>Statut :</strong> <span
                                class="btn btn-{{ $programmeBudgetaire->statut = 0 ? 'secondary' : 'success' }}">{{ $programmeBudgetaire->statut = 0 ? 'Soumis' : 'Validé' }}</span>
                        </li>
                        <div class="mt-4 text-end">
                            <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">
                                Retour à la liste
                            </a>
                            {{-- @can('programme_Bugetaire-delete') --}}
                            <button id="ValidationModal" type="button" class="btn btn-outline-success"
                                title="Valider le programme budgetaire" data-bs-toggle="modal"
                                data-bs-target="#ValidationModal">
                                <i class="bi bi-check"></i> Valider ce programme
                            </button>
                            {{-- @endcan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Objectifs stratégiques associés</h3>
                <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal"
                    data-bs-target="#addObjectifStrategiqueModal">
                    <i class="fas fa-plus"></i> Ajouter un objectif stratégiques
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Libellé</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programmeBudgetaire->objectifsStrategiques as $o_specifique)
                            <tr>
                                <td>{{ 0 }}</td>
                                <td>{{ $o_specifique->libelle }}</td>
                                <td>
                                    <a href="{{ route('objectifStrategiques.show', $o_specifique->id) }}"
                                        class="btn btn-info btn-sm">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun objectif stratégiques associe</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
<!-- Modal Ajouter Indicateur -->
<div class="modal fade" id="addObjectifStrategiqueModal" tabindex="-1" role="dialog"
    aria-labelledby="addObjectifStrategiqueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('objectifStrategiques.store') }}">
                @csrf
                <div class="card-body">

                    <div class="row">
                        <input type="hidden" name="programme_budgetaire_id" value="{{ $programmeBudgetaire->id }}">
                        <input type="hidden" name="os" value="{{ $programmeBudgetaire->id }}">

                        @include('objectifStrategiques.fields')
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('objectifStrategiques.index') }}" class="btn btn-default"> Annuler </a>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Valider un programme --}}
<div class="modal fade" id="ValidationModal" tabindex="-1" aria-labelledby="ValidationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ValidationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="validateModalMessage">
                <p class="alert alert-warning">Vous souhaitez valider ce programme budgétaire en tant que responsable ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('programmebudgetaire.validate', $programmeBudgetaire->id) }}" class="btn btn-warning"
                    id="confirmLockBtn">Confirmer</a>
            </div>
        </div>
    </div>
</div>
