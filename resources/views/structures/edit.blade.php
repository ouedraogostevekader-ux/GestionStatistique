@extends('layouts.admin')
@section('breadcrumb', 'Modifier une structure')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('structures.index') }}">Structures</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        <a class="btn btn-secondary" href="{{ route('structures.index') }}">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Modifier la structure : {{ $structure->nom }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('structures.update', $structure->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nom" class="form-label">Nom de la structure <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                        id="nom" name="nom" value="{{ old('nom', $structure->nom) }}"
                                        placeholder="Entrez le nom de la structure">
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sigle" class="form-label">Sigle <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sigle') is-invalid @enderror"
                                        id="sigle" name="sigle" value="{{ old('sigle', $structure->sigle) }}"
                                        placeholder="Entrez le sigle">
                                    @error('sigle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="type_structure" class="form-label">Type de structure <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('type_structure') is-invalid @enderror"
                                        id="type_structure" name="type_structure">
                                        <option value="">Sélectionnez le type</option>
                                        <option value="ONG"
                                            {{ old('type_structure', $structure->type_structure) == 'ONG' ? 'selected' : '' }}>
                                            ONG</option>
                                        <option value="Association"
                                            {{ old('type_structure', $structure->type_structure) == 'Association' ? 'selected' : '' }}>
                                            Association</option>
                                        <option value="Fondation"
                                            {{ old('type_structure', $structure->type_structure) == 'Fondation' ? 'selected' : '' }}>
                                            Fondation</option>
                                        <option value="Coopérative"
                                            {{ old('type_structure', $structure->type_structure) == 'Coopérative' ? 'selected' : '' }}>
                                            Coopérative</option>
                                        <option value="Entreprise"
                                            {{ old('type_structure', $structure->type_structure) == 'Entreprise' ? 'selected' : '' }}>
                                            Entreprise</option>
                                        <option value="Institution publique"
                                            {{ old('type_structure', $structure->type_structure) == 'Institution publique' ? 'selected' : '' }}>
                                            Institution publique</option>
                                        <option value="Autre"
                                            {{ old('type_structure', $structure->type_structure) == 'Autre' ? 'selected' : '' }}>
                                            Autre</option>
                                    </select>
                                    @error('type_structure')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-lg"></i> Enregistrer
                                            </button>
                                            <button type="reset" class="btn btn-warning">
                                                <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
