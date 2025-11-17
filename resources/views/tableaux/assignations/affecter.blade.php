@extends('layouts.admin')
@section('breadcrumb', 'Nouvelle Assignation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Nouvelle assignation</h2>
                <h4 class="text-primary">{{ $tableau->libelle }}</h4>
                <p class="text-muted mb-1">{{ $tableau->description }}</p>
                <small class="badge bg-info">{{ $tableau->categorie->libelle }}</small>
            </div>
            <div>
                <a href="{{ route('assignations.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux assignations
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations d'assignation</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tableaux.storeAffectation', $tableau->id) }}">
                            @csrf

                            <!-- Structures -->
                            <div class="mb-3">
                                <label class="form-label"><strong class="text-success">Veuillez cocher la case de la ou les
                                        structure(s) à assigner le tableau <span
                                            class="text-danger">*</span></strong></label>
                                <div class="row mt-2">
                                    @if ($structures->count() > 0)
                                        @foreach ($structures as $structure)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="structure_ids[]"
                                                        value="{{ $structure->id }}" id="structure_{{ $structure->id }}"
                                                        {{ in_array($structure->id, old('structure_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="structure_{{ $structure->id }}">
                                                        <strong>{{ $structure->nom }}</strong><br>
                                                        <small class="text-muted">{{ $structure->sigle }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Toutes les structures disponibles ont déjà été assignées à ce tableau.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('structure_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Version -->
                            <div class="mb-3">
                                <label for="version" class="form-label"><strong>Version <span
                                            class="text-danger">*</span></strong></label>
                                <select class="form-select @error('version') is-invalid @enderror" name="version"
                                    data-placeholder="Selectionner" style="width: 100%;">
                                    <option value="">--Veuillez choisir la version--</option>
                                    @foreach ($versions as $version)
                                        <option class="select2-selection__choice" value="{{ $version->id }}">
                                            {{ $version->code }}-{{ $version->annee ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('version')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date début -->
                            <div class="mb-3">
                                <label for="date_debut" class="form-label"><strong>Date de début <span
                                            class="text-danger">*</span></strong></label>
                                <input type="date" name="date_debut" id="date_debut"
                                    class="form-control @error('date_debut') is-invalid @enderror"
                                    value="{{ old('date_debut') }}">
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date fin -->
                            <div class="mb-3">
                                <label for="date_fin" class="form-label"><strong>Date de fin <span
                                            class="text-danger">*</span></strong></label>
                                <input type="date" name="date_fin" id="date_fin"
                                    class="form-control @error('date_fin') is-invalid @enderror"
                                    value="{{ old('date_fin') }}" placeholder="Ex: 31/12/2024, Fin T1, etc.">
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Période -->
                            {{-- <div class="mb-3">
                                <label for="periode" class="form-label">Période</label>
                                <input type="text" class="form-control" name="periode" id="periode" value="{{ old('periode') }}">
                                @error('periode')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <!-- Dates -->
                            {{-- <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_debut" class="form-label">Date début</label>
                                    <input type="date" class="form-control" name="date_debut" id="date_debut" value="{{ old('date_debut') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_fin" class="form-label">Date fin</label>
                                    <input type="date" class="form-control" name="date_fin" id="date_fin" value="{{ old('date_fin') }}">
                                    @error('date_fin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <!-- Bouton -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Affecter les structures
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
