
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
                           
                            <td>
                                <button class="btn expand-btn" type="button" data-bs-toggle="collapse" data-bs-target="#ind{{ $key }}">
                                    <i class="bi bi-eye-fill me-1"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="collapse collapse-content" id="ind{{ $key }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Liste des suivis <span class="btn btn-info">({{ $indicateur->suivis->count() }})</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex justify-content-end">
                                                
                                                <button class="btn btn-primary btn-sm" onclick="openCreateModal('{{ $indicateur->id }}')">
                                                    + Ajouter
                                                </button>
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
                                                    <td>{{ $suivi->periode }}</td>
                                                    <td class="text-danger">{{$suivi->observations}}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary me-2" 
                                                                onclick="openEditModal('{{ $suivi->id }}')">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        
                                                        <button type="button" class="btn btn-outline-danger delete-btn" title="Supprimer"
                                                            data-url="{{ route('suivis.destroy', $suivi->id) }}"
                                                            data-message="<strong>{{ $suivi->resultat_atteint }} - {{ $suivi->cout_effectif }} - {{ $suivi->periode }} </strong> sera supprimé. Continuer ?">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un suivi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="create_indicateur_id" name="indicateur_id">
                    
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
                        <textarea class="form-control" name="observation" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le suivi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_suivi_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Résultat atteint *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_resultat_atteint" name="resultat_atteint" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Coût effectif </label>
                        <input type="number" step="0.01" class="form-control" id="edit_cout_effectif" name="cout_effectif" >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Période </label>
                        <input type="text" class="form-control" id="edit_periode" name="periode" >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observation</label>
                        <textarea class="form-control" id="edit_observation" name="observation" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>