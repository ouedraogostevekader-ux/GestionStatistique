@extends('layouts.admin')
@section('breadcrumb', 'Actions')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Ajouter une action</h3>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-secondary float-right" href="{{ route('actions.index') }}">
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations de l'action</h3>
            </div>

            <form action="{{ route('actions.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="libelle">Libellé <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle"
                            name="libelle" value="{{ old('libelle') }}" required>
                        @error('libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="objectif_strategique_id">Objectif stratégique <span class="text-danger">*</span></label>
                        <select class="form-control @error('objectif_strategique_id') is-invalid @enderror"
                            id="objectif_strategique_id" name="objectif_strategique_id" required>
                            <option value="">Sélectionner un objectif stratégique</option>
                            @foreach ($objectifsStrategiques as $objectif)
                                <option value="{{ $objectif->id }}"
                                    {{ old('objectif_strategique_id') == $objectif->id ? 'selected' : '' }}>
                                    {{ $objectif->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('objectif_strategique_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('actions.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
