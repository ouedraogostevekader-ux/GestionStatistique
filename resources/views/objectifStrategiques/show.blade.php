@extends('layouts.admin')
@section('breadcrumb', 'Objectifs stratégiques')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>
                        objectif strategiques details
                    </h3>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right" href="{{ route('objectifStrategiques.index') }}"> Retour
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('objectifStrategiques.show_fields')
                </div>
            </div>
        </div>
        {{-- Actions --}}
    </div>
    {{-- Indicateurs --}}
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Indicateurs associés</h3>
            @can('indicateur_Programme-create')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addIndicateurModal">
                <i class="fas fa-plus"></i> Ajouter un indicateur
            </button>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Indicateur</th>
                            <th>Unité mesure</th>
                            <th>Valeur cible</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($objectifStrategique->indicateursProgrammes as $indicateur)
                            <tr>
                                <td>{{ $indicateur->libelle }}</td>
                                <td>{{ $indicateur->unite_mesure }}</td>
                                <td>{{ $indicateur->valeur_cible }}</td>
                                <td>
                                    <a href="{{ route('indicateurs-programmes.show', $indicateur->id) }}"
                                        class="btn btn-info btn-sm">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun indicateur associé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-body p-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Actions associées</h3>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addActionModal">
                <i class="fas fa-plus"></i> Ajouter une action
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($objectifStrategique->actions as $o_specifique)
                        <tr>
                            <td>{{ $o_specifique->libelle }}</td>
                            <td>
                                <a href="{{ route('actions.show', $o_specifique->id) }}"
                                    class="btn btn-info btn-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Aucune activité associée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal Ajouter Indicateur -->
    <div class="modal fade" id="addIndicateurModal" tabindex="-1" role="dialog" aria-labelledby="addIndicateurModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('indicateurs-programmes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="objectif_strategique_id" value="{{ $objectifStrategique->id }}">
                    <input type="hidden" name="indicateur" value="{{ $objectifStrategique->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addIndicateurModalLabel">Ajouter un indicateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="libelle_indicateur">Libellé <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="libelle_indicateur" name="libelle" required>
                        </div>
                        <div class="form-group">
                            <label for="unite_mesure">Unité de mesure <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unite_mesure" name="unite_mesure" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valeur_ref">Valeur de référence <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="valeur_ref" name="valeur_ref" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valeur_cible">Valeur cible <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="valeur_cible" name="valeur_cible"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Ajouter une action --}}
    <div class="modal fade" id="addActionModal" tabindex="-1" role="dialog" aria-labelledby="addActionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('actions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="objectif_strategique_id" value="{{ $objectifStrategique->id }}">
                    <input type="hidden" name="action" value="{{ $objectifStrategique->id }}">

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="libelle">Libellé <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="{{ route('actions.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
