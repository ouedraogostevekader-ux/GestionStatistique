@csrf
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="libelle" class="form-label">Libellé</label>
            <input type="text" name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                value="{{ old('libelle', $activite->libelle ?? '') }}" required>
            @error('libelle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="periodes" class="form-label">Périodes</label>
            <select name="periodes[]" id="periodes" class="form-select select2 @error('periodes') is-invalid @enderror"
                multiple required data-placeholder="Sélectionner une ou plusieurs périodes">
                <option value="">-- Sélectionner une ou plusieurs périodes --</option>
                @foreach ($periodes as $periode)
                    <option value="{{ $periode->id }}" class="select2-selection__choice"
                        {{ in_array($periode->id, old('periodes', $activitePeriodes ?? [])) ? 'selected' : '' }}>
                        {{ $periode->libelle }}
                    </option>
                @endforeach
            </select>
            @error('periodes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="annee" class="form-label">Année</label>
            <input type="number" name="annee" id="annee"
                class="form-control @error('annee') is-invalid @enderror"
                value="{{ old('annee', $annee ?? date('Y')) }}" required min="2000" max="2100">
            @error('annee')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="cout_previsionnel" class="form-label">Coût prévisionnel</label>
            <input type="number" step="0.01" name="cout_previsionnel" id="cout_previsionnel"
                class="form-control @error('cout_previsionnel') is-invalid @enderror"
                value="{{ old('cout_previsionnel', $activite->cout_previsionnel ?? '') }}" required>
            @error('cout_previsionnel')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input hidden type="checkbox" name="hors_programme" id="hors_programme" value="1"
                class="form-check-input @error('hors_programme') is-invalid @enderror"
                {{ old('hors_programme', $activite->hors_programme ?? false) ? 'checked' : '' }}>
            <label hidden class="form-check-label" for="hors_programme">Activité hors programme</label>
            @error('hors_programme')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if (!isset($objectifSpecifique))
            <div class="mb-3">
                <label for="objectif_specifique_id" class="form-label">Objectif spécifique</label>
                <select name="objectif_specifique_id" id="objectif_specifique_id"
                    class="form-select @error('objectif_specifique_id') is-invalid @enderror" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach ($objectifs as $obj)
                        <option value="{{ $obj->id }}"
                            {{ old('objectif_specifique_id', $activite->objectif_specifique_id ?? '') == $obj->id ? 'selected' : '' }}>
                            {{ $obj->libelle }}
                        </option>
                    @endforeach
                </select>
                @error('objectif_specifique_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @else
            <input type="hidden" name="objectif_specifique_id" value="{{ $objectifSpecifique->id }}">
        @endif
    </div>
</div>

<!-- Section des indicateurs -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Indicateurs de performance</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addIndicator">
            <i class="bi bi-plus"></i> Ajouter un indicateur
        </button>
    </div>
    <div class="card-body">
        <div id="indicatorsContainer">
            @php
                $existingIndicators = old('indicateurs', isset($activite) ? $activite->indicateursProgrammes : []);
                $indicatorCount = count($existingIndicators) > 0 ? count($existingIndicators) : 1;
            @endphp

            @for ($i = 0; $i < $indicatorCount; $i++)
                @php
                    $indicator = $existingIndicators[$i] ?? null;
                @endphp
                <div class="indicator-item border rounded p-3 mb-3" data-index="{{ $i }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Indicateur {{ $i + 1 }}</h6>
                        @if ($i > 0)
                            <button type="button" class="btn btn-sm btn-outline-danger remove-indicator">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="indicateurs[{{ $i }}][libelle]" class="form-label">Libellé de
                                l'indicateur</label>
                            <input type="text" name="indicateurs[{{ $i }}][libelle]"
                                class="form-control @error('indicateurs.{{ $i }}.libelle') is-invalid @enderror"
                                value="{{ old('indicateurs.' . $i . '.libelle', $indicator->libelle ?? '') }}"
                                required>
                            @error('indicateurs.' . $i . '.libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="indicateurs[{{ $i }}][unite_mesure]" class="form-label">Unité de
                                mesure</label>
                            <input type="text" name="indicateurs[{{ $i }}][unite_mesure]"
                                class="form-control @error('indicateurs.{{ $i }}.unite_mesure') is-invalid @enderror"
                                value="{{ old('indicateurs.' . $i . '.unite_mesure', $indicator->unite_mesure ?? '') }}"
                                placeholder="Ex: %, nombre, km..." required>
                            @error('indicateurs.' . $i . '.unite_mesure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="indicateurs[{{ $i }}][annee]" class="form-label">Année</label>
                            <input type="number" name="indicateurs[{{ $i }}][annee]"
                                class="form-control @error('indicateurs.{{ $i }}.annee') is-invalid @enderror"
                                value="{{ old('indicateurs.' . $i . '.annee', $indicator->annee ?? date('Y')) }}"
                                min="2000" max="2100" required>
                            @error('indicateurs.' . $i . '.annee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="indicateurs[{{ $i }}][valeur_ref]" class="form-label">Valeur de
                                référence</label>
                            <input type="text" name="indicateurs[{{ $i }}][valeur_ref]"
                                class="form-control @error('indicateurs.{{ $i }}.valeur_ref') is-invalid @enderror"
                                value="{{ old('indicateurs.' . $i . '.valeur_ref', $indicator->valeur_ref ?? '') }}"
                                required>
                            @error('indicateurs.' . $i . '.valeur_ref')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="indicateurs[{{ $i }}][valeur_cible]" class="form-label">Valeur
                                cible</label>
                            <input type="text" name="indicateurs[{{ $i }}][valeur_cible]"
                                class="form-control @error('indicateurs.{{ $i }}.valeur_cible') is-invalid @enderror"
                                value="{{ old('indicateurs.' . $i . '.valeur_cible', $indicator->valeur_cible ?? '') }}"
                                required>
                            @error('indicateurs.' . $i . '.valeur_cible')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-success">
        {{ isset($activite) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
    <a href="{{ route('activites.index') }}" class="btn btn-secondary">Annuler</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let indicatorIndex = {{ $indicatorCount }};

        // Ajouter un nouvel indicateur
        document.getElementById('addIndicator').addEventListener('click', function() {
            const container = document.getElementById('indicatorsContainer');
            const newIndicator = createIndicatorHTML(indicatorIndex);
            container.insertAdjacentHTML('beforeend', newIndicator);
            indicatorIndex++;
        });

        // Supprimer un indicateur
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-indicator') || e.target.closest(
                    '.remove-indicator')) {
                const indicatorItem = e.target.closest('.indicator-item');
                indicatorItem.remove();
                updateIndicatorNumbers();
            }
        });

        function createIndicatorHTML(index) {
            return `
            <div class="indicator-item border rounded p-3 mb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Indicateur ${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-indicator">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="indicateurs[${index}][libelle]" class="form-label">Libellé de l'indicateur</label>
                        <input type="text" name="indicateurs[${index}][libelle]" 
                            class="form-control" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="indicateurs[${index}][unite_mesure]" class="form-label">Unité de mesure</label>
                        <input type="text" name="indicateurs[${index}][unite_mesure]" 
                            class="form-control" placeholder="Ex: %, nombre, km..." required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="indicateurs[${index}][annee]" class="form-label">Année</label>
                        <input type="number" name="indicateurs[${index}][annee]" 
                            class="form-control" value="${new Date().getFullYear()}" 
                            min="2000" max="2100" required>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="indicateurs[${index}][valeur_ref]" class="form-label">Valeur de référence</label>
                        <input type="text" name="indicateurs[${index}][valeur_ref]" 
                            class="form-control" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="indicateurs[${index}][valeur_cible]" class="form-label">Valeur cible</label>
                        <input type="text" name="indicateurs[${index}][valeur_cible]" 
                            class="form-control" required>
                    </div>
                </div>
            </div>
        `;
        }

        function updateIndicatorNumbers() {
            const indicators = document.querySelectorAll('.indicator-item');
            indicators.forEach((indicator, index) => {
                indicator.querySelector('h6').textContent = `Indicateur ${index + 1}`;
            });
        }
    });
</script>
