@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ajouter un indicateur</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-secondary float-right" href="{{ route('indicateurs-programmes.index') }}">
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations de l'indicateur</h3>
            </div>

            <form action="{{ route('indicateurs-programmes.store') }}" method="POST">
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
                        <label for="unite_mesure">Unité de mesure <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('unite_mesure') is-invalid @enderror"
                            id="unite_mesure" name="unite_mesure" value="{{ old('unite_mesure') }}" required>
                        @error('unite_mesure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valeur_ref">Valeur de référence <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('valeur_ref') is-invalid @enderror"
                                    id="valeur_ref" name="valeur_ref" value="{{ old('valeur_ref') }}" required>
                                @error('valeur_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valeur_cible">Valeur cible <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('valeur_cible') is-invalid @enderror"
                                    id="valeur_cible" name="valeur_cible" value="{{ old('valeur_cible') }}" required>
                                @error('valeur_cible')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Information :</strong> Sélectionnez un seul élément parmi les trois options ci-dessous selon
                        le niveau de votre indicateur.
                    </div>

                    <div class="form-group">
                        <label for="objectif_specifique_id">Objectif spécifique</label>
                        <select class="form-control @error('objectif_specifique_id') is-invalid @enderror"
                            id="objectif_specifique_id" name="objectif_specifique_id">
                            <option value="">Sélectionner un objectif spécifique</option>
                            @foreach ($objectifsSpecifiques as $objectif)
                                <option value="{{ $objectif->id }}"
                                    {{ old('objectif_specifique_id') == $objectif->id ? 'selected' : '' }}>
                                    {{ $objectif->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('objectif_specifique_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="objectif_strategique_id">Objectif stratégique</label>
                        <select class="form-control @error('objectif_strategique_id') is-invalid @enderror"
                            id="objectif_strategique_id" name="objectif_strategique_id">
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

                    <div class="form-group">
                        <label for="activites_id">Activité</label>
                        <select class="form-control @error('activites_id') is-invalid @enderror" id="activites_id"
                            name="activites_id">
                            <option value="">Sélectionner une activité</option>
                            @foreach ($activites as $activite)
                                <option value="{{ $activite->id }}"
                                    {{ old('activites_id') == $activite->id ? 'selected' : '' }}>
                                    {{ $activite->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('activites_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('indicateurs-programmes.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
