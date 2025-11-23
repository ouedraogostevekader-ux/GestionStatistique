?>

{{-- resources/views/tableaux/edit.blade.php --}}
@extends('layouts.admin')
@section('breadcrumb', 'Modifier le tableau')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Modifier le tableau: {{ $tableau->libelle }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tableaux.update', $tableau->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="libelle" class="form-label"><strong>Libellé</strong></label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                    id="libelle" name="libelle" value="{{ old('libelle', $tableau->libelle) }}" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label"><strong>Description(Source)</strong></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description', $tableau->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="categorie_id" class="form-label"><strong>Catégorie</strong></label>
                                <select class="form-select @error('categorie_id') is-invalid @enderror" id="categorie_id"
                                    name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}"
                                            {{ old('categorie_id', $tableau->categorie_id) == $categorie->id ? 'selected' : '' }}>
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
                                <button type="submit" class="btn btn-success">Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Section Structure existante -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Structure actuelle</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Lignes ({{ $lignes->count() }})</h6>
                                <ul class="list-group list-group-flush">
                                    @forelse($lignes as $ligne)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $ligne->ordre }}. {{ $ligne->nom_ligne }}</span>
                                            @if ($ligne->parent_id)
                                                <small class="text-muted">Sous-ligne</small>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="list-group-item text-muted">Aucune ligne</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Colonnes ({{ $colonnes->count() }})</h6>
                                <ul class="list-group list-group-flush">
                                    @forelse($colonnes as $colonne)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $colonne->ordre }}. {{ $colonne->colonne_ligne }}</span>
                                            @if ($colonne->parent_id)
                                                <small class="text-muted">Sous-colonne</small>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="list-group-item text-muted">Aucune colonne</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
