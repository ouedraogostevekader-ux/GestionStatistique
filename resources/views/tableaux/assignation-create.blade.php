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
                <a href="{{ route('assignations.index', $tableau->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux assignations
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations d'assignation</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('assignations.store', $tableau->id) }}" method="POST">
                            @csrf

                            <!-- Structures -->
                            <div class="mb-3">
                                <label for="structure_ids" class="form-label">
                                    Structures <span class="text-danger">*</span>
                                </label>
                                <div class="row">
                                    @if ($structures->count() > 0)
                                        @foreach ($structures as $structure)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="structure_ids[]"
                                                        value="{{ $structure->id }}" id="structure_{{ $structure->id }}"
                                                        {{ in_array($structure->id, old('structure_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="structure_{{ $structure->id }}">
                                                        <strong>{{ $structure->nom }}</strong>
                                                        <br><small class="text-muted">{{ $structure->sigle }}</small>
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

                            <div class="row">
                                <!-- Statut -->
                                <div class="col-md-6 mb-3">
                                    <label for="statut" class="form-label">
                                        Statut <span class="text-danger">*</span>
                                    </label>
                                    <select name="statut" id="statut"
                                        class="form-select @error('statut') is-invalid @enderror" required>
                                        @foreach ($statuts as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('statut', 0) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Responsable -->
                                <div class="col-md-6 mb-3">
                                    <label for="responsable" class="form-label">Responsable</label>
                                    <input type="text" name="responsable" id="responsable"
                                        class="form-control @error('responsable') is-invalid @enderror"
                                        value="{{ old('responsable') }}" placeholder="Nom du responsable">
                                    @error('responsable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Période -->
                                <div class="col-md-4 mb-3">
                                    <label for="periode" class="form-label">Période</label>
                                    <select name="periode" id="periode"
                                        class="form-select @error('periode') is-invalid @enderror">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($periodes as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('periode') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('periode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Année -->
                                <div class="col-md-4 mb-3">
                                    <label for="annee" class="form-label">Année</label>
                                    <input type="number" name="annee" id="annee"
                                        class="form-control @error('annee') is-invalid @enderror"
                                        value="{{ old('annee', date('Y')) }}" min="2000" max="2100"
                                        placeholder="{{ date('Y') }}">
                                    @error('annee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date début -->
                                <div class="col-md-4 mb-3">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" name="date_debut" id="date_debut"
                                        class="form-control @error('date_debut') is-invalid @enderror"
                                        value="{{ old('date_debut') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Date fin -->
                                <div class="col-md-4 mb-3">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" name="date_fin" id="date_fin"
                                        class="form-control @error('date_fin') is-invalid @enderror"
                                        value="{{ old('date_fin') }}" placeholder="Ex: 31/12/2024, Fin T1, etc.">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Version -->
                                <div class="col-md-4 mb-3">
                                    <label for="version" class="form-label">Version</label>
                                    <input type="text" name="version" id="version"
                                        class="form-control @error('version') is-invalid @enderror"
                                        value="{{ old('version') }}" placeholder="Ex: v1.0, 2024.1, etc.">
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Source -->
                                <div class="col-md-4 mb-3">
                                    <label for="source" class="form-label">Source</label>
                                    <input type="text" name="source" id="source"
                                        class="form-control @error('source') is-invalid @enderror"
                                        value="{{ old('source') }}" placeholder="Ex: Direction, Ministère, etc.">
                                    @error('source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('assignations.index', $tableau->id) }}" class="btn btn-secondary me-2">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary"
                                    {{ $structures->count() == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-check"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panneau d'aide -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Aide
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6>Assignation de tableau</h6>
                        <p class="small">
                            L'assignation permet de confier la saisie d'un tableau à une ou plusieurs structures.
                        </p>

                        <h6>Statuts disponibles :</h6>
                        <ul class="small">
                            <li><strong>En attente :</strong> Assignation créée mais pas encore commencée</li>
                            <li><strong>En cours :</strong> Saisie en cours de réalisation</li>
                            <li><strong>Terminé :</strong> Saisie complète et validée</li>
                            <li><strong>Suspendu :</strong> Assignation temporairement interrompue</li>
                        </ul>

                        <h6>Conseils :</h6>
                        <ul class="small">
                            <li>Vous pouvez assigner le même tableau à plusieurs structures</li>
                            <li>Définissez un responsable pour faciliter le suivi</li>
                            <li>Précisez la période et l'année pour éviter les confusions</li>
                            <li>La version permet de différencier les éditions du tableau</li>
                        </ul>
                    </div>
                </div>

                @if ($structures->count() == 0)
                    <div class="card border-warning mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>Attention
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small mb-2">
                                Toutes les structures disponibles ont déjà été assignées à ce tableau.
                            </p>
                            <p class="small mb-0">
                                Vous pouvez modifier les assignations existantes ou supprimer certaines assignations pour en
                                créer de nouvelles.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélection/Désélection de toutes les structures
            const selectAllBtn = document.createElement('button');
            selectAllBtn.type = 'button';
            selectAllBtn.className = 'btn btn-sm btn-outline-success mb-2';
            selectAllBtn.textContent = 'Tout sélectionner';

            const structuresContainer = document.querySelector('.row');
            if (structuresContainer && document.querySelectorAll('input[name="structure_ids[]"]').length > 0) {
                structuresContainer.insertBefore(selectAllBtn, structuresContainer.firstChild);

                selectAllBtn.addEventListener('click', function() {
                    const checkboxes = document.querySelectorAll('input[name="structure_ids[]"]');
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked;
                    });

                    this.textContent = allChecked ? 'Tout sélectionner' : 'Tout désélectionner';
                });
            }

            // Validation côté client
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const checkedStructures = document.querySelectorAll(
                    'input[name="structure_ids[]"]:checked');

                if (checkedStructures.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins une structure.');
                    return false;
                }
            });

            // Auto-complétion pour le responsable (optionnel)
            const responsableInput = document.getElementById('responsable');
            responsableInput.addEventListener('focus', function() {
                // Ici, vous pourriez ajouter une fonctionnalité d'auto-complétion
                // basée sur les utilisateurs existants
            });
        });
    </script>
@endsection
