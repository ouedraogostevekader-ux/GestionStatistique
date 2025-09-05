@csrf
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
        <select name="periodes[]" id="periodes" class="form-select @error('periodes') is-invalid @enderror" multiple
            required>
            <option value="">-- Sélectionner une ou plusieurs périodes --</option>
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

    <div class="mb-3">
        <label for="annee" class="form-label">Année</label>
        <input type="number" name="annee" id="annee" class="form-control @error('annee') is-invalid @enderror"
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

    <button type="submit" class="btn btn-success">
        {{ isset($activite) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
    <a href="{{ route('activites.index') }}" class="btn btn-secondary">Annuler</a>
</div>
