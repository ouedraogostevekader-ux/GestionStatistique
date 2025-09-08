@extends('layouts.admin')
@section('breadcrumb', 'Objectifs spéciques')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Détails de l'objectif spécifique</h3>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a class="btn btn-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        @can('objectif_Specifique-edit')
                            <a class="btn btn-primary"
                                href="{{ route('objectifs-specifiques.edit', $objectifSpecifique->id) }}">Modifier</a>
                        @endcan
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
                            <dd class="col-sm-8">{{ $objectifSpecifique->libelle }}</dd>

                            <dt class="col-sm-4">Action :</dt>
                            <dd class="col-sm-8">
                                {{ $objectifSpecifique->action ? $objectifSpecifique->action->libelle : 'N/A' }}</dd>

                            <dt class="col-sm-4">Objectif stratégique :</dt>
                            <dd class="col-sm-8">
                                {{ $objectifSpecifique->action ? $objectifSpecifique->action->objectifStrategique->libelle : 'N/A' }}
                            </dd>

                            <dt class="col-sm-4">Créé le :</dt>
                            <dd class="col-sm-8">{{ $objectifSpecifique->created_at->format('d/m/Y à H:i') }}</dd>

                            <dt class="col-sm-4">Modifié le :</dt>
                            <dd class="col-sm-8">{{ $objectifSpecifique->updated_at->format('d/m/Y à H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        {{-- Indicateurs --}}
        <div class="card">
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
                            @forelse($objectifSpecifique->indicateursProgrammes as $indicateur)
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
        {{-- Activités --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Activités associées <span
                        class="text-success">({{ $objectifSpecifique->activites->count() }})</span>
                </h3>
                @can('activite-create')
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addActiviteModal">
                        <i class="fas fa-plus"></i> Ajouter une activité
                    </button>
                @endcan
            </div>
            <div class="card-body p-0">
                @if ($objectifSpecifique->activites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Activité</th>
                                    <th>Période</th>
                                    <th>Coût prévisionnel</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objectifSpecifique->activites as $activite)
                                    <tr>
                                        <td>{{ $activite->libelle }}</td>
                                        <td>
                                            @foreach ($activite->periodes as $periode)
                                                {{ $periode->libelle }},
                                            @endforeach
                                        </td>
                                        <td>{{ number_format($activite->cout_previsionnel, 0, ',', ' ') }} F CFA</td>
                                        <td>
                                            {{--  @can('activite-list')
                                            <a href="{{ route('activites.show', $activite->id) }}"
                                                class="btn btn-info btn-sm">Voir</a>
                                        @endcan --}}
                                            <form action="{{ route('activites.destroy', $activite->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <div class='btn-group'>
                                                    <a href="{{ route('activites.show', $activite->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir"><i
                                                            class="bi bi-eye-fill"></i></a>

                                                    {{-- <a href="" class="btn btn-warning btn-xs" id="btnEdit"
                                                        title="Modifier"><i class="bi bi-pencil-fill"></i></a>
 --}}
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Supprimer cette activité ?')"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                @else
                    <tr>
                        <td colspan="4" class="text-center text-muted">Aucune activité associée</td>
                    </tr>
                @endif
            </div>
        </div>

        <!-- Modal Ajouter Activité -->
        <div class="modal fade" id="addActiviteModal" tabindex="-1" role="dialog" aria-labelledby="addActiviteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('activites.store') }}" method="POST">
                        <input type="hidden" name="activite" value="{{ $objectifSpecifique->id }}">
                        @include('activites.form-create-modal')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Indicateur -->
        <div class="modal fade" id="addIndicateurModal" tabindex="-1" role="dialog"
            aria-labelledby="addIndicateurModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('indicateurs-programmes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="objectif_specifique_id" value="{{ $objectifSpecifique->id }}">
                        <input type="hidden" name="indicateur" value="{{ $objectifSpecifique->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addIndicateurModalLabel">Ajouter un indicateur</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="libelle_indicateur">Libellé <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="libelle_indicateur" name="libelle"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="unite_mesure">Unité de mesure <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unite_mesure" name="unite_mesure"
                                    required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="valeur_ref">Valeur de référence <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="valeur_ref" name="valeur_ref"
                                            required>
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
    </div>
    <script src="{{ asset('js/activites-table.js') }}"></script>
@endsection
