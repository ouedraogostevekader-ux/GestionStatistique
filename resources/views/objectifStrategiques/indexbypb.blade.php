@extends('layouts.admin')
@section('breadcrumb', 'Objectifs stratégiques')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item h6 active">Liste des objectifs stratégiques du
                            "{{ $programme->libelle }}"<span
                                class="btn btn-info">({{ $objectifStrategiques->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('objectif_Strategique-create')
                            <a class="btn btn-primary" href="{{ route('objectifStrategiques.create') }}">
                                <i class="bi bi-plus"></i> Ajouter
                            </a>
                        @endcan
                    </div>
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
