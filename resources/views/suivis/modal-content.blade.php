<div class="modal-header">
    <h5 class="modal-title">Ajouter un suivi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <form method="POST" action="{{ route('suivis.store') }}">
        @csrf

        <input type="hidden" value="{{$indicateur_programme_id}}" name="indicateur_programme_id">
        <div class="mb-3">
            <label class="form-label">Résultat atteint *</label>
            <input type="number" step="0.01" class="form-control" name="resultat_atteint" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Coût effectif </label>
            <input type="number" step="0.01" class="form-control" name="cout_effectif" >
        </div>

        <div class="mb-3">
            <label class="form-label">Période </label>
            <input type="text" class="form-control" name="periode" >
        </div>

        <div class="mb-3">
            <label class="form-label">Observation</label>
            <textarea class="form-control" name="observations" rows="3"></textarea>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">
                Créer
            </button>
        </div>
    </form>
</div>
