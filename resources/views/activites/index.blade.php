@extends('layouts.admin')
@section('breadcrumb', 'Activités')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des activités<span
                                class="btn btn-info">({{ $activites->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        <a class="btn btn-primary" href="{{ route('activites.create') }}">
                            Ajouter
                        </a>
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3">
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
        <div class="col-sm-10">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Libellé</th>
                                <th>Période</th>
                                <th>Coût prévisionnel</th>
                                <th>Objectif spécifique</th>
                                <th>Date création</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activites as $key => $activite)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $activite->libelle }}</td>
                                    <td>{{ $activite->periode }}</td>
                                    <td>{{ number_format($activite->cout_previsionnel, 2, ',', ' ') }} FCFA</td>
                                    <td>{{ $activite->objectifSpecifique->libelle ?? '-' }}</td>
                                    <td>{{ $activite->created_at->format('d/m/Y, H:i') }}</td>
                                    <td>
                                        <form action="{{ route('activites.destroy', $activite->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <div class='btn-group'>
                                                <a href="{{ route('activites.show', $activite->id) }}"
                                                    class="btn btn-info btn-xs" title="Voir"><i
                                                        class="bi bi-eye-fill"></i></a>

                                                <a href="{{ route('activites.edit', $activite->id) }}"
                                                    class="btn btn-primary btn-xs" title="Modifier"><i
                                                        class="bi bi-pencil-fill"></i></a>

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
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Libellé</th>
                                <th>Période</th>
                                <th>Coût prévisionnel</th>
                                <th>Objectif spécifique</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Ton JS personnalisé -->
    <script src="{{ asset('js/activites-table.js') }}"></script>
@endsection
