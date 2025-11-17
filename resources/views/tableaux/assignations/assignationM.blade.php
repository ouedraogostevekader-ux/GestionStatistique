@extends('layouts.admin')
@section('breadcrumb', 'Assignation multiple')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Nouvelles assignations</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('assignations.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour aux assignations
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations des assignations</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('assignations.storeM') }}" method="POST">
                            @csrf

                            <!-- Structure -->
                            <div class="mb-3">
                                <label for="structure_id" class="form-label"><strong>Structure <span
                                            class="text-danger">*</span></strong></label>
                                <select name="structure_id" id="structure_id"
                                    class="form-select @error('structure_id') is-invalid @enderror">
                                    <option value="">--Veuillez choisir une structure--</option>
                                    @foreach ($structures as $structure)
                                        <option value="{{ $structure->id }}">
                                            {{ $structure->sigle }} : {{ $structure->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('structure_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tableaux (multi-select) -->
                            <div class="mb-3">
                                <label for="tableau_ids" class="form-label"><strong>Tableaux <span
                                            class="text-danger">*</span></strong></label>
                                <select name="tableau_ids[]" id="tableau_ids" class="form-select select2" multiple>
                                    @foreach ($tableaux as $tableau)
                                        <option value="{{ $tableau->id }}">{{ $tableau->libelle }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Maintenez CTRL pour sélectionner plusieurs tableaux</small>
                            </div>

                            <!-- Version -->
                            <div class="mb-3">
                                <label for="version_id" class="form-label"><strong>Version <span
                                            class="text-danger">*</span></strong></label>
                                <select class="form-select @error('version_id') is-invalid @enderror" name="version_id">
                                    <option value="">--Veuillez choisir la version--</option>
                                    @foreach ($versions as $version)
                                        <option value="{{ $version->id }}">
                                            {{ $version->code }} - {{ $version->annee }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('version_id')
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

                            <!-- Boutons -->
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('assignations.dashboard') }}" class="btn btn-secondary me-2">Annuler</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Enregistrer
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tableau_ids').select2({
                placeholder: "Sélectionner un ou plusieurs tableaux",
                width: "100%"
            });
            $('#structure_id').select2({
                placeholder: "Sélectionner une structure",
                width: "100%"
            });
        });
    </script>
@endpush
