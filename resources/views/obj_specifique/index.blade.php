@extends('layouts.admin')
@section('breadcrumb', 'Objectifs spéciques')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des objectifs spécifiques<span
                                class="btn btn-info">({{ $objectifsSpecifiques->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('objectif_Specifique-create')
                            <a class="btn btn-primary" id="btnAjouter" href="{{ route('objectifs-specifiques.create') }}">
                                <i class="bi bi-plus"></i> Ajouter
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <form method="GET" action="{{ route('objectifs-specifiques.index') }}" class="mb-3 row gx-4">
            <div class="col-md-2">
                <label for="annee" class="form-label fw-bold">Année</label>
                <input type="number" name="annee" id="annee" class="form-control" value="{{ request('annee') }}"
                    placeholder="Ex: 2025">
            </div>

            <div class="col-md-3">
                <label for="programme_budgetaire_id" class="form-label fw-bold">Programme Budgetaire</label>
                <select name="programme_budgetaire_id" id="programme_budgetaire_id" class="form-select">
                    <option value="">Toutes</option>
                    @foreach ($programmes as $programme)
                        <option value="{{ $programme->id }}"
                            {{ request('programme_budgetaire_id') == $programme->id ? 'selected' : '' }}>
                            {{ $programme->numero_programme }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label for="action_id" class="form-label fw-bold">Action</label>
                <select name="action_id" id="action_id" class="form-select">
                    <option value="">Toutes</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action->id }}" {{ request('action_id') == $action->id ? 'selected' : '' }}>
                            {{ $action->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                <a href="{{ route('actions.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="content px-3">
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body px-3">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example2">
                        <thead>
                            <tr>
                                <th>Objectif spécifique</th>
                                <th>Action</th>
                                <th>Objectif stratégique</th>
                                <th>Nb activités</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($objectifsSpecifiques as $objectif)
                                <tr>
                                    <td>{{ $objectif->libelle }}</td>
                                    <td>
                                        @if ($objectif->action)
                                            {{ $objectif->action->libelle }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($objectif->action && $objectif->action->objectifStrategique)
                                            {{ $objectif->action->objectifStrategique->libelle }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $objectif->activites->count() }}</td>
                                    <td>
                                        <div class='btn-group'>
                                            @can('objectif_Specifique-list')
                                                <a href="{{ route('objectifs-specifiques.show', $objectif->id) }}"
                                                    class="btn btn-info btn-xs" title="Voir">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                            @endcan
                                            @can('objectif_Specifique-edit')
                                                <a href="{{ route('objectifs-specifiques.edit', $objectif->id) }}"
                                                    class="btn btn-warning btn-xs" title="Modifier">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            @can('objectif_Specifique-delete')
                                                <button type="button" class="btn btn-outline-danger delete-btn"
                                                    title="Supprimer"
                                                    data-url="{{ route('objectifs-specifiques.destroy', $objectif->id) }}"
                                                    data-message="L'objectif spécifique <strong>{{ $objectif->libelle }}</strong> sera supprimé. Continuer ?">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-right">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
