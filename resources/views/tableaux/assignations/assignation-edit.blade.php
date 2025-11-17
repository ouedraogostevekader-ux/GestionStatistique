@extends('layouts.admin')

@section('breadcrumb', 'Modifier une assignation')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Modifier l'assignation</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('assignations.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('assignations.update', $assignation->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="structure_id" class="form-label">Structure</label>
                        <select name="structure_id" id="structure_id" class="form-select" required>
                            @foreach ($structures as $structure)
                                <option value="{{ $structure->id }}"
                                    {{ $assignation->structure_id == $structure->id ? 'selected' : '' }}>
                                    {{ $structure->sigle }} - {{ $structure->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tableau_id" class="form-label">Tableau</label>
                        <select name="tableau_id" id="tableau_id" class="form-select" required>
                            @foreach ($tableaux as $tableau)
                                <option value="{{ $tableau->id }}"
                                    {{ $assignation->tableau_id == $tableau->id ? 'selected' : '' }}>
                                    {{ $tableau->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="version_id" class="form-label">Version</label>
                        <select name="version_id" id="version_id" class="form-select">
                            @foreach ($versions as $version)
                                <option value="{{ $version->id }}"
                                    {{ $assignation->version_id == $version->id ? 'selected' : '' }}>
                                    {{ $version->code }} - {{ $version->annee }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="date" class="form-control" name="date_debut" id="date_debut"
                                value="{{ old('date_debut', $assignation->date_debut) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" name="date_fin" id="date_fin"
                                value="{{ old('date_fin', $assignation->date_fin) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
