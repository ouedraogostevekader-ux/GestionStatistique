@extends('layouts.admin')
@section('breadcrumb', 'Suivis')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/suivi.css') }}" type="text/css" />
    <section class="content-header">
        <div class="container px-2" id="filters-section">

            <div class="mb-2 d-flex row">
                {{-- Filtre selon les objectifs strategiques --}}
                <div class="col-sm-3">
                    <label for="filterObjStrat" class="form-label">Objectif stratégique :</label>
                    <select id="filterObjStrat" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($objectifStrategiques as $objStrat)
                            <option value="{{ $objStrat->id }}" data-libelle="{{ $objStrat->libelle }}">
                                {{ $objStrat->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="filterAction" class="form-label">Action :</label>
                    <select id="filterAction" class="form-select" disabled>
                        <option value="">-- Veuillez choisir --</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action->id }}" data-parent="{{ $action->objectif_strategique_id }}" data-libelle="{{ $action->libelle }}">
                                {{ $action->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtre selon les objectifs specifiques --}}
                <div class="col-sm-3">
                    <label for="filterObjectif" class="form-label">Objectif spécifique :</label>
                    <select id="filterObjectif" class="form-select" disabled>
                        <option value="">-- Veuillez choisir --</option>
                        @foreach ($objectifSpecifiques as $obj)
                            <option value="{{ $obj->id }}" 
                                    data-parent="{{ $obj->action_id }}" 
                                    data-libelle="{{ $obj->libelle }}">
                                {{ $obj->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtre selon les activites --}}
                <div class="col-sm-3">
                    <label for="filterActivite" class="form-label">Activité :</label>
                    <select id="filterActivite" class="form-select" disabled>
                        <option value="">-- Veuillez choisir --</option>
                        @foreach ($activites as $activite)
                            <option value="{{ $activite->id }}" 
                                    data-parent="{{ $activite->objectif_specifique_id }}" 
                                    data-libelle="{{ $activite->libelle }}">
                                {{ $activite->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bouton de réinitialisation --}}
            <div class="mb-2">
                <button id="resetFilters" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-refresh me-1"></i>Réinitialiser
                </button>
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
    <script src="{{ asset('js/suivi2.js') }}"></script>

@endsection