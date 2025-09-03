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
        <label for="periode" class="form-label">Période</label>
        <input type="text" name="periode" id="periode" class="form-control @error('periode') is-invalid @enderror"
            value="{{ old('periode', $activite->periode ?? '') }}" required>
        @error('periode')
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

    <button type="submit" class="btn btn-success">
        {{ isset($activite) ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
    <a href="{{ route('activites.index') }}" class="btn btn-secondary">Annuler</a>
</div>
