{{-- Formulaire de creation de rapport d'activité --}}
<div class="modal-header">
    <h5 class="modal-title">Générer un rapport</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <form method="POST" action="{{ route('rapportActivites.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Programme d'activité *</label>
            <select class="form-select" name="programme_activite_id" required disabled>
                <option value="{{$programmeActivite->id ?? null}}">{{$programmeActivite->libelle ?? "Aucun programme d'activité pour l'année ".now()->year}}</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Libelle *</label>
            <input type="text" class="form-control" name="libelle" required>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <label class="form-label">Periode rapport *</label>
                <select class="form-select" name="periode_rapport" required>
                    @foreach ($periodes as $key=>$periodeRapport)
                        <option value="{{$key}}">{{$periodeRapport}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label">Date *</label>
                <input type="date" class="form-control" name="date_rapport" value="{{ date('Y-m-d') }}" disabled required>
            </div>

            <div class="col-sm-4">
                <label class="form-label">Année *</label>
                <input type="number" class="form-control" name="annee" value="{{ date('Y') }}" disabled required>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">
                Générer
            </button>
        </div>
    </form>
</div>