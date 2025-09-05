
<div class="card-header">
    <h4 class="mb-0">
        <i class="fas fa-table me-2"></i>
        Liste des indicateurs
    </h4>
</div>
<div class="card-body p-0">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-map-marker-alt me-2"></i>Libellé</th>
                        <th><i class="fas fa-euro-sign me-2"></i>Unité mesure</th>
                        <th><i class="fas fa-chart-line me-2"></i>Reference</th>
                        <th><i class="fas fa-star me-2"></i>Cible</th>
                        <th><i class="fas fa-star me-2"></i>Objectif spécifique</th>
                        <th><i class="fas fa-star me-2"></i>Objectif stratégique</th>
                        <th><i class="fas fa-star me-2"></i>Activité</th>
                        <th><i class="fas fa-eye me-2"></i>Suivis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($indicateurProgrammes as $key => $indicateur)
                        <tr>
                            <td>{{ $indicateur->libelle }}</td>
                            <td>{{ $indicateur->unite_mesure }}</td>
                            <td>{{ $indicateur->valeur_ref }}</td>
                            <td>{{ $indicateur->valeur_cible }}</td>
                            <td>{{ $indicateur->objectifSpecifique->libelle ?? "" }}</td>
                            <td>{{ $indicateur->objectifStrategique->libelle ?? "" }}</td>
                            <td>{{ $indicateur->activite->libelle ?? "" }}</td>
                           
                            <td class="text-center">
                                <button class="btn btn-outline-primary btn-sm expand-btn" type="button" data-bs-toggle="collapse" data-bs-target="#ind{{ $key }}" aria-expanded="false">
                                    <i class="bi bi-eye-fill me-1"></i><span class="badge bg-secondary ms-1">{{ $indicateur->suivis->count() }}</span>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="collapse voirvisible" id="ind{{ $key }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Évolutions de l'indicateur
                                                <span class="badge bg-primary ms-2">{{ $indicateur->suivis->count() }}</span>
                                            </h6>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex justify-content-end">
                                                @can('suivi-create')
                                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#suiviModal"
                                                        data-url="{{ route('suivis.createM', $indicateur->id) }}">
                                                        <i class="bi bi-plus"></i> Nouvelle evolution
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub-table">
                                        @if($indicateur->suivis->count() > 0)
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Résultat atteint</th>
                                                    <th>Coût effectif</th>
                                                    <th>Période</th>
                                                    <th>Observation</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($indicateur->suivis as $suivi)
                                                <tr>
                                                    <td>{{ $suivi->resultat_atteint }}</td>
                                                    <td>{{ $suivi->cout_effectif }}</td>
                                                    <td>{{ $suivi->periode->libelle ?? "Indisponible" }}</td>
                                                    <td class="text-danger">{{$suivi->observations}}</td>
                                                    <td>
                                                       @can('suivi-edit')
                                                        <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                                            data-bs-target="#suiviModal" title="Modifier"
                                                            data-url="{{ route('suivis.edit', $suivi->id) }}">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        @endcan
                                                        
                                                        @can('suivi-delete')
                                                        <button type="button" class="btn btn-outline-danger delete-btn" title="Supprimer"
                                                            data-url="{{ route('suivis.destroy', $suivi->id) }}"
                                                            data-message="<strong>{{ $suivi->resultat_atteint }} - {{ $suivi->cout_effectif }} - {{ $suivi->periode }} </strong> sera supprimé. Continuer ?">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                        @endcan
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                        @else
                                            <div class="alert alert-info">
                                                Aucun suivi enregistré pour cet indicateur.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="suiviModal" tabindex="-1" aria-labelledby="suiviModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Le contenu sera chargé dynamiquement ici via AJAX -->
        </div>
    </div>
</div>
