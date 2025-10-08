<div class="col-sm-3 mb-3">
    <label>Code *</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $version->code ?? null) }}" required>
</div>
<div class="col-sm-3 mb-3">
    <label>Libellé</label>
    <input type="text" name="libelle" value="{{ old('libelle', $version->libelle ?? null) }}" class="form-control">
</div>
<div class="col-sm-3 mb-3">
    <label>Année</label>
    <input type="text" name="annee" value="{{ old('annee', $version->annee ?? null) }}" class="form-control">
</div>
