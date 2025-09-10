@extends('layouts.admin')
@section('breadcrumb', 'Activités')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des activités hors programme<span
                                class="btn btn-success">({{ $activites->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('activite-create')
                            <a class="btn btn-primary" id="btnAjouter"
                                href="{{ route('rapportActivites.hors.programme.create') }}">
                                <i class="bi bi-plus"></i> + Ajouter
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3" id="filters-section">
                <div>
                    <label for="filterPeriode" class="form-label">Filtrer par période :</label>
                    <select id="filterPeriode" class="form-select">
                        <option value="">-- Toutes --</option>
                        @foreach ($activites->pluck('periode')->unique() as $periode)
                            <option value="{{ $periode }}">{{ $periode }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filterObjectif" class="form-label">Filtrer par objectif spécifique :</label>
                    <select id="filterObjectif" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($objectifs as $obj)
                            <option value="{{ $obj->libelle }}">{{ $obj->libelle }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </section>

    <section class="content px-3">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Activité</th>
                                <th>Période</th>
                                <th>Coût prévisionnel</th>
                                <th>Objectif spécifique</th>
                                <th>Indicateurs</th>
                                <th>Date création</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activites as $key => $activite)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $activite->libelle }}</td>
                                    <td>
                                        @foreach ($activite->periodes as $periode)
                                            {{ $periode->libelle }},
                                        @endforeach
                                    </td>
                                    <td>{{ number_format($activite->cout_previsionnel, 2, ',', ' ') }} FCFA</td>
                                    <td>{{ $activite->objectifSpecifique->libelle ?? '-' }}</td>
                                    <td>{{ $activite->indicateursProgrammes->count() }}</td>
                                    <td>{{ $activite->updated_at->format('d/m/Y, H:i') }}</td>
                                    <td>
                                        <form action="{{ route('rapportActivites.hors.programme.delete', $activite->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <div class='btn-group'>
                                                @can('activite-list')
                                                    <a href="{{ route('rapportActivites.hors.programme.show', $activite->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                @endcan
                                                @can('activite-edit')
                                                    <a href="{{ route('rapportActivites.edit', $activite->id) }}"
                                                        class="btn btn-warning btn-xs" id="btnEdit" title="Modifier"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                @endcan
                                                @can('activite-delete')
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Supprimer cette activité ?')"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Ton JS personnalisé -->
    <script src="{{ asset('js/activites-hp-table.js') }}"></script>
@endsection
