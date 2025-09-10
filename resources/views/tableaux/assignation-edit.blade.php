@extends('layouts.admin')
@section('breadcrumb', 'Modifier Assignation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Modifier l'assignation</h2>
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
                        <h5 class="card-title mb-0">Modification de l'assignation</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('assignations.update', [$tableau->id, $assignation->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Structure actuelle -->
                            <div class="mb-3">
                                <label class="form-label">Structure assignée actuellement</label>
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <strong>{{ $assignation->structure->nom }}</strong>
                                        <small class="text-muted ms-2">({{ $assignation->structure->sigle }})</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Nouvelle structure -->
                            <div class="mb-3">
                                <label for="structure_id" class="form-label">
                                    Changer de structure <span class="text-danger">*</span>
                                </label>
                                <select name="structure_id" id="structure_id"
                                    class="form-select @error('structure_id') is-invalid @enderror" required>
                                    <option value="{{ $assignation->structure_id }}">
                                        {{ $assignation->structure->nom }} ({{ $assignation->structure->sigle }}) - Actuel
                                    </option>
                                    @foreach ($structures as $structure)
                                        @if ($structure->id != $assignation->structure_id)
                                            <option value="{{ $structure->id }}"
                                                {{ old('structure_id') == $structure->id ? 'selected' : '' }}>
                                                {{ $structure->nom }} ({{ $structure->sigle }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('structure_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Sélectionnez une nouvelle structure ou gardez la structure
                                    actuelle</small>
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
                                                {{ old('statut', $assignation->statut) == $value ? 'selected' : '' }}>
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
                                        value="{{ old('responsable', $assignation->responsable) }}"
                                        placeholder="Nom du responsable">
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
                                                {{ old('periode', $assignation->periode) == $value ? 'selected' : '' }}>
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
                                        value="{{ old('annee', $assignation->annee) }}" min="2000" max="2100"
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
                                        value="{{ old('date_debut', $assignation->date_debut ? $assignation->date_debut->format('Y-m-d') : '') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Date fin -->
                                <div class="col-md-4 mb-3">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="text" name="date_fin" id="date_fin"
                                        class="form-control @error('date_fin') is-invalid @enderror"
                                        value="{{ old('date_fin', $assignation->date_fin) }}"
                                        placeholder="Ex: 31/12/2024, Fin T1, etc.">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Version -->
                                <div class="col-md-4 mb-3">
                                    <label for="version" class="form-label">Version</label>
                                    <input type="text" name="version" id="version"
                                        class="form-control @error('version') is-invalid @enderror"
                                        value="{{ old('version', $assignation->version) }}"
                                        placeholder="Ex: v1.0, 2024.1, etc.">
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Source -->
                                <div class="col-md-4 mb-3">
                                    <label for="source" class="form-label">Source</label>
                                    <input type="text" name="source" id="source"
                                        class="form-control @error('source') is-invalid @enderror"
                                        value="{{ old('source', $assignation->source) }}"
                                        placeholder="Ex: Direction, Ministère, etc.">
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panneau d'informations -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informations
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6>Assignation créée :</h6>
                        <p class="small">{{ $assignation->created_at->format('d/m/Y à H:i') }}</p>

                        <h6>Dernière modification :</h6>
                        <p class="small">{{ $assignation->updated_at->format('d/m/Y à H:i') }}</p>

                        @if ($assignation->date_debut)
                            <h6>Date de début :</h6>
                            <p class="small">{{ Carbon\Carbon::parse($assignation->date_debut)->format('d/m/Y') }}</p>
                        @endif

                        @if ($assignation->date_fin)
                            <h6>Date de fin :</h6>
                            <p class="small">{{ $assignation->date_fin }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Attention
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small">
                            <strong>Changement de structure :</strong> Si vous changez la structure assignée,
                            toutes les données saisies par l'ancienne structure resteront associées à l'assignation,
                            mais la nouvelle structure n'y aura pas accès automatiquement.
                        </p>
                        <p class="small">
                            <strong>Statut :</strong> Le changement de statut peut affecter la visibilité
                            et les permissions de modification du tableau.
                        </p>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightning me-2"></i>Actions rapides
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="setStatus(2)">
                                <i class="bi bi-check-circle me-1"></i>Marquer comme terminé
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="setStatus(1)">
                                <i class="bi bi-play-circle me-1"></i>Marquer en cours
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="setStatus(3)">
                                <i class="bi bi-pause-circle me-1"></i>Suspendre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour définir rapidement le statut
            window.setStatus = function(status) {
                const statusSelect = document.getElementById('statut');
                statusSelect.value = status;

                // Visual feedback
                statusSelect.classList.add('border-primary');
                setTimeout(() => {
                    statusSelect.classList.remove('border-primary');
                }, 1000);
            };

            // Confirmation avant changement de structure
            const structureSelect = document.getElementById('structure_id');
            const originalStructureId = '{{ $assignation->structure_id }}';

            structureSelect.addEventListener('change', function() {
                if (this.value !== originalStructureId) {
                    const confirmed = confirm(
                        'Attention : Changer de structure peut affecter l\'accès aux données existantes. ' +
                        'Êtes-vous sûr de vouloir continuer ?'
                    );

                    if (!confirmed) {
                        this.value = originalStructureId;
                    }
                }
            });

            // Validation des dates
            const dateDebutInput = document.getElementById('date_debut');
            const dateFinInput = document.getElementById('date_fin');

            dateDebutInput.addEventListener('change', function() {
                if (this.value && dateFinInput.value) {
                    // Vous pouvez ajouter ici une validation pour s'assurer 
                    // que la date de début est antérieure à la date de fin
                }
            });
        });
    </script>
@endsection
