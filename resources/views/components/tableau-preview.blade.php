<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $tableau->libelle }}</h6>
    </div>
    <div class="card-body">
        @if($tableau->lignesTableau->count() > 0 && $tableau->colonnesTableau->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            @foreach($tableau->colonnesTableau->take(3) as $colonne)
                                <th class="text-center small">{{ Str::limit($colonne->colonne_ligne, 10) }}</th>
                            @endforeach
                            @if($tableau->colonnesTableau->count() > 3)
                                <th class="text-center small">...</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableau->lignesTableau->take(3) as $ligne)
                        <tr>
                            <td class="small">{{ Str::limit($ligne->nom_ligne, 15) }}</td>
                            @foreach($tableau->colonnesTableau->take(3) as $colonne)
                                <td class="text-center small">-</td>
                            @endforeach
                            @if($tableau->colonnesTableau->count() > 3)
                                <td class="text-center small">...</td>
                            @endif
                        </tr>
                        @endforeach
                        @if($tableau->lignesTableau->count() > 3)
                        <tr>
                            <td class="small">...</td>
                            @foreach($tableau->colonnesTableau->take(3) as $colonne)
                                <td class="text-center small">...</td>
                            @endforeach
                            @if($tableau->colonnesTableau->count() > 3)
                                <td class="text-center small">...</td>
                            @endif
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted small">Structure non définie</p>
        @endif
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                {{ $tableau->lignesTableau->count() }} lignes × {{ $tableau->colonnesTableau->count() }} colonnes
            </small>
            <div>
                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-sm btn-outline-primary">
                    Voir
                </a>
            </div>
        </div>
    </div>
</div>