@extends('layouts.admin')
@section('breadcrumb', 'Rapports d\'activités')
@section('content')
    <link rel="stylesheet" href="{{asset('css/suivi.css')}}" type="text/css" />
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Rapports d'activités
                    </h1>    
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('rapportActivite-create')
                        
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rapportActiviteModal"
                            data-url="{{ route('rapportActivites.create') }}">
                            <i class="bi bi-plus"></i> Generer un rapport
                        </button>
                        @endcan

                        @can('rapportActivite-create')
                        
                        <button class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#rapportActiviteVisualiserModal"
                            data-url="{{ route('rapportActivites.visualiser') }}">
                            <i class="bi bi-plus"></i> Visualiser
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3">
                {{-- Filtre selon le programme d'activites --}}
                <div>
                    <label for="filterProgramme" class="form-label">Filtrer par programme d'activités :</label>
                    <select id="filterProgramme" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($programmeActivites as $programme)
                            <option value="{{ $programme->libelle }}">{{ $programme->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtre selon la periode --}}
                <div>
                    <label for="filterPeriode" class="form-label">Filtrer par periode :</label>
                    <select id="filterPeriode" class="form-select">
                        <option value="">-- Toutes --</option>
                        @foreach ($periodes as $key=>$periode)
                            <option value="{{ $key }}">{{ $periode }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtre selon l'annee --}}
                <div>
                    <label for="filterAnnee" class="form-label">Filtrer par annee :</label>
                    @php
                        $currentYear = date('Y');
                    @endphp
                    <select id="filterAnnee" class="form-select">
                        <option value="">-- Toutes --</option>
                        @for ($year = intval($currentYear - 10); $year <= $currentYear+1; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
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
            @include('rapportActivites.table')
        </div>
    </section>
    <script src="{{ asset('js/rapport.js') }}"></script>
@endsection
