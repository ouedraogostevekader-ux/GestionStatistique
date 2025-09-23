
@extends('layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Modifier la catégorie</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('categories.update', $categorie->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="libelle" class="form-label">Libellé</label>
            <input type="text" name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                   value="{{ old('libelle', $categorie->libelle) }}" required>

            @error('libelle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
