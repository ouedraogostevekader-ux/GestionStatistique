@extends('layouts.admin')
@section('breadcrumb', 'Tableaux')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Créer un nouveau tableau</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tableaux.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="libelle" class="form-label">Libellé *</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                    id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="categorie_id" class="form-label">Catégorie *</label>
                                <select class="form-select @error('categorie_id') is-invalid @enderror" id="categorie_id"
                                    name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}"
                                            {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tableaux.index') }}" class="btn btn-secondary">Annuler</a>
                                <button type="submit" class="btn btn-success">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
