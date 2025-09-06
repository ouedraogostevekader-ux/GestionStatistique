@extends('layouts.admin')
@section('breadcrumb', 'Suivis')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/suivi.css') }}" type="text/css" />
    <section class="content-header">
        <div class="container px-2" id="filters-section">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        {{-- <a class="btn btn-primary" href="{{ route('suivis.create') }}">
                            <i class="bi bi-plus"></i> Ajouter
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3">
                {{-- Filtre selon les activites --}}
                <div>
                    <label for="filterActivite" class="form-label">Filtrer par activité :</label>
                    <select id="filterActivite" class="form-select">
                        <option value="">-- Toutes --</option>
                        @foreach ($activites as $activite)
                            <option value="{{ $activite->libelle }}">{{ $activite->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtre selon les objectifs specifiques --}}
                <div>
                    <label for="filterObjectif" class="form-label">Filtrer par objectif spécifique :</label>
                    <select id="filterObjectif" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($objectifSpecifiques as $obj)
                            <option value="{{ $obj->libelle }}">{{ $obj->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtre selon les objectifs strategiques --}}
                <div>
                    <label for="filterObjStrat" class="form-label">Filtrer par objectif stratégique :</label>
                    <select id="filterObjStrat" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($objectifStrategiques as $objStrat)
                            <option value="{{ $objStrat->libelle }}">{{ $objStrat->libelle }}</option>
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
        <div class="clearfix"></div>
        <div class="card">
            @include('suivis.table')
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="suiviModal" tabindex="-1" aria-labelledby="suiviModalLabel" aria-hidden="true" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Le contenu sera chargé dynamiquement ici via AJAX -->
            </div>
        </div>
    </div>
    <script src="{{ asset('js/suivi.js') }}"></script>
@endsection
