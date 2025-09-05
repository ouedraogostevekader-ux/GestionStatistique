@extends('layouts.admin')
@section('breadcrumb', 'Dupliquer un Programme Budgétaire')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Dupliquer un Programme Budgétaire</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('programmes-budgetaires.store-duplicate') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programme_source_id" class="form-label">Programme à dupliquer *</label>
                                <select name="programme_source_id" id="programme_source_id"
                                    class="form-select @error('programme_source_id') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un programme --</option>
                                    @foreach ($programmes as $programme)
                                        <option value="{{ $programme->id }}"
                                            {{ old('programme_source_id') == $programme->id ? 'selected' : '' }}>
                                            {{ $programme->numero_programme }} - {{ $programme->libelle }}
                                            ({{ $programme->annee }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('programme_source_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="annee" class="form-label">Année du nouveau programme *</label>
                                <input type="number" name="annee" id="annee"
                                    class="form-control @error('annee') is-invalid @enderror"
                                    value="{{ old('annee', date('Y') + 1) }}" min="2000" max="2100" required>
                                @error('annee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nouveau_libelle" class="form-label">Nouveau libellé *</label>
                                <input type="text" name="nouveau_libelle" id="nouveau_libelle"
                                    class="form-control @error('nouveau_libelle') is-invalid @enderror"
                                    value="{{ old('nouveau_libelle') }}" required>
                                @error('nouveau_libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nouveau_numero" class="form-label">Nouveau numéro *</label>
                                <input type="text" name="nouveau_numero" id="nouveau_numero"
                                    class="form-control @error('nouveau_numero') is-invalid @enderror"
                                    value="{{ old('nouveau_numero') }}" required>
                                @error('nouveau_numero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Doit être unique</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="dupliquer_activites" id="dupliquer_activites"
                                class="form-check-input" value="1"
                                {{ old('dupliquer_activites') ? 'checked' : 'checked' }}>
                            <label for="dupliquer_activites" class="form-check-label">
                                Dupliquer également les activités et leurs périodes
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Information :</strong> Cette action va dupliquer :
                        <ul class="mb-0 mt-2">
                            <li>Le programme budgétaire</li>
                            <li>Tous les objectifs stratégiques</li>
                            <li>Toutes les actions</li>
                            <li>Tous les objectifs spécifiques</li>
                            <li id="activites-info">Les activités et leurs périodes (si coché)</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-copy"></i> Dupliquer
                        </button>
                        <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('dupliquer_activites');
            const activitesInfo = document.getElementById('activites-info');

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    activitesInfo.textContent = 'Les activités et leurs périodes (si coché)';
                } else {
                    activitesInfo.textContent = 'Les activités ne seront pas dupliquées';
                }
            });
        });
    </script>
@endsection
