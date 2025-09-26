@extends('layouts.admin')
@section('breadcrumb', 'Objectifs stratégiques')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des objectifs stratégiques<span
                                class="btn btn-info">({{ $objectifStrategiques->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('objectif_Strategique-create')
                            <a class="btn btn-primary" id="btnAjouter" href="{{ route('objectifStrategiques.create') }}">
                                <i class="bi bi-plus"></i> Ajouter
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3">
                {{-- Filtre selon progtramme budgetaire --}}
                <div>
                    <label for="filterProgramme" class="form-label fw-bold">Filtrer par programme budgetaire :</label>
                    <select id="filterProgramme" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($programmeBudgetaires as $programme)
                            <option value="{{ $programme->libelle }}">{{ $programme->libelle }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </section>
    <section class="content px-3">
        <div class="clearfix"></div>
        <div class="card">
            @include('objectifStrategiques.table')
        </div>
    </section>
    <script src="{{ asset('js/objStrategique-table.js') }}"></script>
@endsection
