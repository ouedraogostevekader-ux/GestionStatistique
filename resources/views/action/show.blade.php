@extends('layouts.admin')
@section('breadcrumb', 'Actions')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Détails de l'action</h3>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a class="btn btn-secondary" href="{{ route('actions.index') }}">Retour</a>
                        <a class="btn btn-primary" href="{{ route('actions.edit', $action->id) }}">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations générales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Libellé :</dt>
                            <dd class="col-sm-8">{{ $action->libelle }}</dd>

                            <dt class="col-sm-4">Objectif stratégique :</dt>
                            <dd class="col-sm-8">
                                {{ $action->objectifStrategique ? $action->objectifStrategique->libelle : '-' }}</dd>

                            <dt class="col-sm-4">Créé le :</dt>
                            <dd class="col-sm-8">{{ $action->created_at->format('d/m/Y à H:i') }}</dd>

                            <dt class="col-sm-4">Modifié le :</dt>
                            <dd class="col-sm-8">{{ $action->updated_at->format('d/m/Y à H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Objectifs spécifiques associés</h3>
                <!-- Bouton qui déclenche la modal -->
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#addObjectifModal">
                    <i class="fas fa-plus"></i> Ajouter un objectif spécifique
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Objectif spécifique</th>
                                <th>Nombre d'activités</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($action->objectifsSpecifiques as $objectifSpecifique)
                                <tr>
                                    <td>{{ $objectifSpecifique->libelle }}</td>
                                    <td>{{ $objectifSpecifique->activites->count() }}</td>
                                    <td>
                                        <a href="{{ route('objectifs-specifiques.show', $objectifSpecifique->id) }}"
                                            class="btn btn-info btn-sm">Voir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun objectif spécifique associé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Objectif Spécifique -->
        <!-- Modal -->
        <div class="modal fade" id="addObjectifModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- En-tête du modal -->
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un objectif spécifique</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Corps du modal -->
                    <div class="modal-body">
                        <form action="{{ route('objectifs-specifiques.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action_id" value="{{ $action->id }}">
                            <div class="mb-3">
                                <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                                <input type="text" id="libelle" name="libelle" class="form-control" required>
                            </div>
                            <!-- Pied du modal -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
