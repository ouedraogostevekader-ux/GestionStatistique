<div class="card-header">
    <h4 class="mb-0">
        <i class="fas fa-table me-2"></i>
        Liste des indicateurs
        <span id="indicateurCount" class="badge bg-primary ms-2">{{ $indicateurProgrammes->count() }}</span>
    </h4>
</div>
<div class="card-body p-0">
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="example7">
                <thead>
                    <tr>
                        <th>Objectif stratégique</th>
                        <th>Objectif spécifique</th>
                        <th>Activité</th>
                        <th>Indicateur</th>
                        <th>Unité mesure</th>
                        <th>Reference</th>
                        <th>Cible</th>
                        <th>Suivis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($indicateurProgrammes as $key => $indicateur)
                        <tr class="indicateur-row" data-obj-strat-id="{{ $indicateur->objectifStrategique->id ?? '' }}"
                            data-obj-spec-id="{{ $indicateur->objectifSpecifique->id ?? '' }}"
                            data-activite-id="{{ $indicateur->activite->id ?? '' }}"
                            data-obj-strat-libelle="{{ $indicateur->objectifStrategique->libelle ?? '' }}"
                            data-obj-spec-libelle="{{ $indicateur->objectifSpecifique->libelle ?? '' }}"
                            data-activite-libelle="{{ $indicateur->activite->libelle ?? '' }}">
                            <td>{{ $indicateur->objectifStrategique->libelle ?? 'N/A' }}</td>
                            <td>{{ $indicateur->objectifSpecifique->libelle ?? 'N/A' }}</td>
                            <td>{{ $indicateur->activite->libelle ?? 'N/A' }}</td>
                            <td>{{ $indicateur->libelle }}</td>
                            <td>{{ $indicateur->unite_mesure }}</td>
                            <td>{{ $indicateur->valeur_ref }}</td>
                            <td>{{ $indicateur->valeur_cible }}</td>

                            <td>
                                @can('suivi-list')
                                    <button class="btn btn-outline-primary btn-sm expand-btn" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#ind{{ $key }}"
                                        aria-expanded="false">
                                        <i class="bi bi-eye-fill me-1"></i><span
                                            class="badge bg-secondary">{{ $indicateur->suivis->count() }}</span>
                                    </button>
                                @endcan
                            </td>

                        </tr>
                        <tr class="indicateur-details" data-parent-row="{{ $key }}">
                            <td colspan="8" class="p-0">
                                <div class="collapse voirvisible" id="ind{{ $key }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Évolutions de l'indicateur
                                                <span
                                                    class="badge bg-primary ms-2">{{ $indicateur->suivis->count() }}</span>
                                            </h6>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex justify-content-end">
                                                @can('suivi-create')
                                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#suiviModal"
                                                        data-url="{{ route('suivis.createM', $indicateur->id) }}">
                                                        <i class="bi bi-plus"></i> Nouvelle evaluation
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub-table">
                                        @if ($indicateur->suivis->count() > 0)
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
                                                            <td>{{ $suivi->getPeriode->libelle ?? 'Indisponible' }}
                                                            </td>
                                                            <td class="text-danger">{{ $suivi->observations }}</td>
                                                            <td>
                                                                @can('suivi-edit')
                                                                    <button class="btn btn-sm btn-outline-primary me-2"
                                                                        data-bs-toggle="modal" data-bs-target="#suiviModal"
                                                                        title="Modifier"
                                                                        data-url="{{ route('suivis.edit', $suivi->id) }}">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                    </button>
                                                                @endcan

                                                                @can('suivi-delete')
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger delete-btn"
                                                                        title="Supprimer"
                                                                        data-url="{{ route('suivis.destroy', $suivi->id) }}"
                                                                        data-message="<strong>{{ $suivi->resultat_atteint }} - {{ $suivi->cout_effectif }} - {{ $suivi->getPeriode->libelle }} </strong> sera supprimé. Continuer ?">
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
                <thead>
                    <tr>
                        <th>Objectif stratégique</th>
                        <th>Objectif spécifique</th>
                        <th>Activité</th>
                        <th>Indicateur</th>
                        <th>Unité mesure</th>
                        <th>Reference</th>
                        <th>Cible</th>
                        <th>Suivis</th>
                    </tr>
                </thead>
            </table>

            {{-- Message quand aucun indicateur ne correspond aux filtres --}}
            <div id="noIndicatorsMessage" class="alert alert-info text-center" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>
                Aucun indicateur ne correspond aux critères de filtrage sélectionnés.
            </div>
        </div>
    </div>
</div>
