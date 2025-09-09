<!-- Formulaire spécifique pour les activités hors programme -->

@csrf
@if (isset($activite))
    @method('PUT')
@endif

<div class="row">
    <!-- Colonne principale -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    Activité Hors Programme
                </h5>
                <small>Activité imprévue ne faisant pas partie de la programmation initiale</small>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libellé de l'activité <span
                            class="text-danger">*</span></label>
                    <textarea name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror" rows="3"
                        required placeholder="Décrivez l'activité imprévue...">{{ old('libelle', $activite->libelle ?? '') }}</textarea>
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="periodes" class="form-label">Périodes concernées <span
                                    class="text-danger">*</span></label>
                            <select name="periodes[]" id="periodes"
                                class="form-select select2 @error('periodes') is-invalid @enderror" multiple required
                                data-placeholder="Sélectionner les périodes">
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->id }}"
                                        {{ in_array($periode->id, old('periodes', $activitePeriodes ?? [])) ? 'selected' : '' }}>
                                        {{ $periode->libelle }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periodes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                            <input type="number" name="annee" id="annee"
                                class="form-control @error('annee') is-invalid @enderror"
                                value="{{ old('annee', $activite->annee ?? date('Y')) }}" required min="2000"
                                max="2100">
                            @error('annee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="justification" class="form-label">Justification/Contexte</label>
                    <textarea name="justification" id="justification" class="form-control @error('justification') is-invalid @enderror"
                        rows="3" placeholder="Pourquoi cette activité a-t-elle été nécessaire ?">{{ old('justification', $activite->justification ?? '') }}</textarea>
                    @error('justification')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="statut" id="statut"
                                class="form-select @error('statut') is-invalid @enderror" required>
                                <option value="">-- Sélectionner le statut --</option>
                                <option value="planifiee"
                                    {{ old('statut', $activite->statut ?? '') == 'planifiee' ? 'selected' : '' }}>
                                    Planifiée
                                </option>
                                <option value="en_cours"
                                    {{ old('statut', $activite->statut ?? '') == 'en_cours' ? 'selected' : '' }}>
                                    En cours
                                </option>
                                <option value="terminee"
                                    {{ old('statut', $activite->statut ?? '') == 'terminee' ? 'selected' : '' }}>
                                    Terminée
                                </option>
                                <option value="annulee"
                                    {{ old('statut', $activite->statut ?? '') == 'annulee' ? 'selected' : '' }}>
                                    Annulée
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="priorite" class="form-label">Niveau de priorité</label>
                            <select name="priorite" id="priorite"
                                class="form-select @error('priorite') is-invalid @enderror">
                                <option value="">-- Sélectionner --</option>
                                <option value="faible"
                                    {{ old('priorite', $activite->priorite ?? '') == 'faible' ? 'selected' : '' }}>
                                    Faible
                                </option>
                                <option value="normale"
                                    {{ old('priorite', $activite->priorite ?? '') == 'normale' ? 'selected' : '' }}>
                                    Normale
                                </option>
                                <option value="elevee"
                                    {{ old('priorite', $activite->priorite ?? '') == 'elevee' ? 'selected' : '' }}>
                                    Élevée
                                </option>
                                <option value="urgente"
                                    {{ old('priorite', $activite->priorite ?? '') == 'urgente' ? 'selected' : '' }}>
                                    Urgente
                                </option>
                            </select>
                            @error('priorite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Évaluation/Suivi -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line"></i>
                    Évaluation et Suivi
                </h5>
            </div>
            <div class="card-body">
                <div id="evaluations-container">
                    @foreach ($periodes as $periode)
                        <div class="periode-evaluation mb-4 p-3 border rounded" data-periode="{{ $periode->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-calendar-alt"></i> {{ $periode->libelle }}
                                </h6>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input periode-active"
                                        id="periode_{{ $periode->id }}" data-periode="{{ $periode->id }}"
                                        {{ isset($evaluations[$periode->libelle]) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="periode_{{ $periode->id }}">
                                        Évaluer cette période
                                    </label>
                                </div>
                            </div>

                            <div class="evaluation-fields"
                                style="display: {{ isset($evaluations[$periode->libelle]) ? 'block' : 'none' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Coût effectif (€)</label>
                                            <input type="number" step="0.01"
                                                name="evaluations[{{ $periode->libelle }}][cout_effectif]"
                                                class="form-control" placeholder="0.00"
                                                value="{{ old('evaluations.' . $periode->libelle . '.cout_effectif', $evaluations[$periode->libelle]->cout_effectif ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Taux de réalisation (%)</label>
                                            <input type="number" min="0" max="100"
                                                name="evaluations[{{ $periode->libelle }}][taux_realisation]"
                                                class="form-control" placeholder="0"
                                                value="{{ old('evaluations.' . $periode->libelle . '.taux_realisation', $evaluations[$periode->libelle]->taux_realisation ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre de bénéficiaires</label>
                                            <input type="number" min="0"
                                                name="evaluations[{{ $periode->libelle }}][beneficiaires]"
                                                class="form-control" placeholder="0"
                                                value="{{ old('evaluations.' . $periode->libelle . '.beneficiaires', $evaluations[$periode->libelle]->beneficiaires ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Résultats obtenus</label>
                                            <textarea name="evaluations[{{ $periode->libelle }}][resultats]" class="form-control" rows="3"
                                                placeholder="Décrivez les résultats concrets...">{{ old('evaluations.' . $periode->libelle . '.resultats', $evaluations[$periode->libelle]->resultats ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Difficultés rencontrées</label>
                                            <textarea name="evaluations[{{ $periode->libelle }}][difficultes]" class="form-control" rows="3"
                                                placeholder="Quelles difficultés avez-vous rencontrées ?">{{ old('evaluations.' . $periode->libelle . '.difficultes', $evaluations[$periode->libelle]->difficultes ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observations générales</label>
                                    <textarea name="evaluations[{{ $periode->libelle }}][observations]" class="form-control" rows="2"
                                        placeholder="Commentaires, recommandations...">{{ old('evaluations.' . $periode->libelle . '.observations', $evaluations[$periode->libelle]->observations ?? '') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date de début</label>
                                            <input type="date"
                                                name="evaluations[{{ $periode->libelle }}][date_debut]"
                                                class="form-control"
                                                value="{{ old('evaluations.' . $periode->libelle . '.date_debut', $evaluations[$periode->libelle]->date_debut ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date de fin</label>
                                            <input type="date"
                                                name="evaluations[{{ $periode->libelle }}][date_fin]"
                                                class="form-control"
                                                value="{{ old('evaluations.' . $periode->libelle . '.date_fin', $evaluations[$periode->libelle]->date_fin ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i>
                {{ isset($activite) ? 'Mettre à jour' : 'Enregistrer' }}
            </button>

        </div>
    </div>

    <!-- Colonne latérale -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i>
                    Guide d'utilisation
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Activités hors programme</h6>
                    <p class="small mb-2">
                        Les activités hors programme sont des activités imprévues qui n'étaient pas dans la
                        planification initiale.
                    </p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary">Évaluation</h6>
                    <ul class="small mb-0">
                        <li><strong>Coût effectif</strong> : Montant réellement dépensé</li>
                        <li><strong>Taux de réalisation</strong> : Pourcentage d'achèvement</li>
                        <li><strong>Bénéficiaires</strong> : Nombre de personnes touchées</li>
                        <li><strong>Résultats</strong> : Impacts concrets obtenus</li>
                    </ul>
                </div>

                <div class="alert alert-info small">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Astuce :</strong> Cochez les périodes pour lesquelles vous souhaitez faire une
                    évaluation.
                </div>
            </div>
        </div>

        <!-- Statistiques si édition -->
        @if (isset($activite))
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Résumé
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h4 text-success mb-0">{{ count($evaluations) }}</div>
                                <small class="text-muted">Évaluations</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h4 text-info mb-0">
                                    {{ number_format(collect($evaluations)->sum('cout_effectif'), 0, ',', ' ') }}€
                                </div>
                                <small class="text-muted">Coût total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


<style>
    .periode-evaluation {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff !important;
    }

    .periode-evaluation.active {
        background-color: #e3f2fd;
        border-left-color: #2196f3 !important;
    }

    .evaluation-fields {
        background-color: white;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    .card-header.bg-warning {
        border-bottom: 2px solid #f0ad4e;
    }

    .card-header.bg-info {
        border-bottom: 2px solid #5bc0de;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodeCheckboxes = document.querySelectorAll('.periode-active');

        periodeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const periodeId = this.dataset.periode;
                const evaluationFields = this.closest('.periode-evaluation').querySelector(
                    '.evaluation-fields');
                const periodeDiv = this.closest('.periode-evaluation');

                if (this.checked) {
                    evaluationFields.style.display = 'block';
                    periodeDiv.classList.add('active');
                } else {
                    evaluationFields.style.display = 'none';
                    periodeDiv.classList.remove('active');

                    // Vider les champs de cette période
                    evaluationFields.querySelectorAll('input, textarea').forEach(field => {
                        field.value = '';
                    });
                }
            });

            // Initialiser l'état au chargement
            checkbox.dispatchEvent(new Event('change'));
        });

        // Gestion du statut pour afficher des informations contextuelles
        const statutSelect = document.getElementById('statut');
        if (statutSelect) {
            statutSelect.addEventListener('change', function() {
                const evaluationSection = document.querySelector('.card-header.bg-info');
                const title = evaluationSection.querySelector('.card-title');

                switch (this.value) {
                    case 'planifiee':
                        title.innerHTML =
                            '<i class="fas fa-calendar-plus"></i> Planification et Prévisions';
                        break;
                    case 'en_cours':
                        title.innerHTML = '<i class="fas fa-spinner"></i> Suivi en Cours';
                        break;
                    case 'terminee':
                        title.innerHTML = '<i class="fas fa-check-circle"></i> Évaluation Finale';
                        break;
                    case 'annulee':
                        title.innerHTML = '<i class="fas fa-times-circle"></i> Bilan de l\'Annulation';
                        break;
                    default:
                        title.innerHTML = '<i class="fas fa-chart-line"></i> Évaluation et Suivi';
                }
            });
        }
    });
</script>
