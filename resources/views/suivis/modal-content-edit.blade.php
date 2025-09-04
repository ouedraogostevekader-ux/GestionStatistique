<div class="modal-header">
    <h5 class="modal-title">Modifier le suivi</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <form method="POST" action="{{ route('suivis.update', $suivi->id) }}">
        @csrf
        @method('PUT')

        <input type="hidden" value="{{$suivi->indicateur_programme_id}}" name="indicateur_programme_id">

        <div class="mb-3">
            <label class="form-label">Résultat atteint *</label>
            <input type="number" step="0.01" class="form-control" name="resultat_atteint" value="{{$suivi->resultat_atteint}}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Coût effectif </label>
            <input type="number" step="0.01" class="form-control" value="{{$suivi->cout_effectif}}" name="cout_effectif" >
        </div>

        <div class="mb-3">
            <label class="form-label">Période </label>
            <select class="form-select" name="periode">
                @foreach ($periodes as $key=>$periode)
                    <option value="{{$key}}" {{$suivi->periode == $key ? 'selected' : ''}}>{{$periode}}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Observation</label>
            <textarea class="form-control" name="observations" value="{{$suivi->observations}}" rows="3"></textarea>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
