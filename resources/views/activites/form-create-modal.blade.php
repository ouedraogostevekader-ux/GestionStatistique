@csrf
<div class="col-md-8">
    <div class="mb-3">
        <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
        <input type="text" name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
            value="{{ old('libelle') }}" required>
        @error('libelle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="periodes" class="form-label">Périodes <span class="text-danger">*</span></label>
        <select name="periodes[]" id="periodes"
            class="form-select {{ !isset($objectifSpecifique) ? 'select2' : '' }} @error('periodes') is-invalid @enderror"
            multiple required data-placeholder="Sélectionner une ou plusieurs périodes">
            <option value="">-- Sélectionner une ou plusieurs périodes --</option>
            @foreach ($periodes as $periode)
                <option value="{{ $periode->id }}" {{ in_array($periode->id, old('periodes', [])) ? 'selected' : '' }}>
                    {{ $periode->libelle }}
                </option>
            @endforeach
        </select>
        @error('periodes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
        <input type="number" name="annee" id="annee" class="form-control @error('annee') is-invalid @enderror"
            value="{{ old('annee', date('Y')) }}" required min="2000" max="2100">
        @error('annee')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="cout_previsionnel" class="form-label">Coût prévisionnel <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="cout_previsionnel" id="cout_previsionnel"
            class="form-control @error('cout_previsionnel') is-invalid @enderror" value="{{ old('cout_previsionnel') }}"
            required>
        @error('cout_previsionnel')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if (!isset($objectifSpecifique))
        <div class="mb-3">
            <label for="objectif_specifique_id" class="form-label">Objectif spécifique <span
                    class="text-danger">*</span></label>
            <select name="objectif_specifique_id" id="objectif_specifique_id"
                class="form-select @error('objectif_specifique_id') is-invalid @enderror" required>
                <option value="">-- Sélectionner --</option>
                @foreach ($objectifs as $obj)
                    <option value="{{ $obj->id }}"
                        {{ old('objectif_specifique_id') == $obj->id ? 'selected' : '' }}>
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
