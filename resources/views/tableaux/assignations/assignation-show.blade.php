@extends('layouts.admin')

@section('breadcrumb', 'Détail assignation')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Détails de l'assignation</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="mt-4 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                    @if (auth()->user()->isAdmin() || auth()->user()->structure?->sigle === 'DGESS' || auth()->user()->can('assignation-edit'))
                        {{-- Bouton Relance si non validé --}}
                        @if ($assignation->statut != 3 && !$assignation->version?->isLocked())
                            <form action="{{ route('assignations.relancer', $assignation->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Confirmez-vous l\'envoi d\'une relance officielle à cette structure ?')">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm text-dark shadow-sm">
                                    <i class="bi bi-bell-fill"></i> Relancer la structure
                                </button>
                            </form>
                        @endif

                        {{-- Boutons d'arbitrage si en attente de validation (statut 2) --}}
                        @if ($assignation->statut == 2 && !$assignation->version?->isLocked())
                            <form action="{{ route('assignations.valider', $assignation->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Confirmez-vous la validation définitive de ces données statistiques ? Cette action verrouillera la soumission.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                    <i class="bi bi-check-circle-fill"></i> Valider définitivement
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRejetAssignation">
                                <i class="bi bi-x-circle-fill"></i> Rejeter la soumission
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('assignations.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-2">
                    <h3 class="card-title mb-3">{{ $assignation->tableau->libelle ?? 'Tableau inconnu' }}</h3>
                </div>

                <div class="row mb-2">
                    <h5 class="card-title mb-3">{{ $assignation->tableau->description ?? 'Tableau inconnu' }}</h5>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Structure :</strong></div>
                    <div class="col-md-8">{{ $assignation->structure->sigle ?? '' }} -
                        {{ $assignation->structure->nom ?? '' }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4"><strong>Version :</strong></div>
                    <div class="col-md-8">{{ $assignation->version->code ?? '' }} - {{ $assignation->version->annee ?? '' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4"><strong>Date de début :</strong></div>
                    <div class="col-md-8">
                        {{ $assignation->date_debut ? \Carbon\Carbon::parse($assignation->date_debut)->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4"><strong>Date de fin :</strong></div>
                    <div class="col-md-8">
                        {{ $assignation->date_fin ? \Carbon\Carbon::parse($assignation->date_fin)->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4"><strong>Statut :</strong></div>
                    <div class="col-md-8">
                        @switch($assignation->statut)
                            @case(0)
                                <span class="badge bg-secondary">En attente</span>
                            @break

                            @case(1)
                                <span class="badge bg-info">En cours</span>
                            @break

                            @case(2)
                                <span class="badge bg-warning text-dark">Transmis / En attente de validation</span>
                            @break

                            @case(3)
                                <span class="badge bg-success">Validé / Terminé</span>
                            @break

                            @case(4)
                                <span class="badge bg-danger">Rejeté / À corriger</span>
                            @break

                            @default
                                <span class="badge bg-light text-dark">Inconnu</span>
                        @endswitch

                        @if ($assignation->isEnRetard())
                            <span class="badge bg-danger ms-2"><i class="bi bi-exclamation-triangle-fill"></i> En retard</span>
                        @elseif ($assignation->isEcheanceProche())
                            <span class="badge bg-warning text-dark ms-2"><i class="bi bi-clock-history"></i> Échéance proche</span>
                        @endif

                        @if ($assignation->relances_count > 0)
                            <span class="badge bg-light text-dark border ms-2">
                                <i class="bi bi-bell"></i> {{ $assignation->relances_count }} relance(s)
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Alertes de contexte --}}
                @if ($assignation->statut == 3)
                    <div class="alert alert-success d-flex align-items-center mt-3 mb-0" role="alert">
                        <i class="bi bi-shield-check fs-4 me-3"></i>
                        <div>
                            <strong>Soumission validée définitivement par la DGESS</strong>
                            <div class="small">
                                Validée le {{ $assignation->date_validation ? \Carbon\Carbon::parse($assignation->date_validation)->format('d/m/Y à H:i') : '' }}
                                @if ($assignation->validePar) par {{ $assignation->validePar->nom_prenom }} @endif.
                                Les données sont désormais consolidées et verrouillées pour l'Annuaire officiel.
                            </div>
                        </div>
                    </div>
                @elseif ($assignation->statut == 4)
                    <div class="alert alert-danger d-flex align-items-start mt-3 mb-0" role="alert">
                        <i class="bi bi-exclamation-octagon-fill fs-4 me-3 mt-1"></i>
                        <div>
                            <strong>Soumission rejetée pour correction</strong>
                            <div class="small mb-1">
                                Rejetée le {{ $assignation->date_rejet ? \Carbon\Carbon::parse($assignation->date_rejet)->format('d/m/Y à H:i') : '' }}
                                @if ($assignation->rejetePar) par {{ $assignation->rejetePar->nom_prenom }} @endif.
                            </div>
                            <div class="p-2 bg-white text-danger rounded border border-danger small">
                                <strong>Motif de la DGESS :</strong> {{ $assignation->motif_rejet }}
                            </div>
                        </div>
                    </div>
                @elseif ($assignation->statut == 2)
                    <div class="alert alert-warning d-flex align-items-center mt-3 mb-0" role="alert">
                        <i class="bi bi-hourglass-split fs-4 me-3"></i>
                        <div>
                            <strong>Soumission transmise — En attente d'instruction par la DGESS</strong>
                            <div class="small">Vérifiez les données matricielles ci-dessous avant de procéder à la validation officielle ou au rejet motivé.</div>
                        </div>
                    </div>
                @endif

                @if ($assignation->version?->isLocked())
                    <div class="alert alert-dark d-flex align-items-center mt-3 mb-0" role="alert">
                        <i class="bi bi-lock-fill fs-4 me-3"></i>
                        <div>
                            <strong>Version de l'Annuaire verrouillée</strong>
                            <div class="small">L'édition de cette version est scellée pour publication officielle. Aucune modification n'est permise.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-2">
                    <h3 class="card-title">
                        Données saisies pour la version :
                        <span class="text-primary">
                            {{ $assignation->version->code ?? '' }} - {{ $assignation->version->libelle ?? '' }}
                        </span>
                    </h3>
                </div>

                @if ($assignation->statut >= 2 || $soumissions->isNotEmpty())
                    @php
                        $assignationId = $assignation->id;
                        $structureId = $assignation->structure_id;

                        // Regrouper les soumissions par cellule_id
                        $soumissionsParCellule = $soumissions
                            ->where('assignation_id', $assignationId)
                            ->where('structure_id', $structureId)
                            ->keyBy('cellule_id');

                        $lignes = \App\Models\LigneTableau::where('tableau_id', $assignation->tableau_id)
                            ->whereNull('parent_id')
                            ->orderBy('ordre')
                            ->get();

                        $colonnes = \App\Models\ColonneTableau::where('tableau_id', $assignation->tableau_id)
                            ->whereNull('parent_id')
                            ->orderBy('ordre')
                            ->get();
                    @endphp
                    <div class="row mb-2">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                {{-- 🔹 En-tête principale --}}
                                <thead class="table-light">
                                    <tr>
                                        @foreach ($lignes as $ligne)
                                            @if ($ligne->ordre == 1)
                                                <th rowspan="2" class="text-center">{{ $ligne->nom_ligne }}</th>
                                            @endif
                                        @endforeach

                                        @foreach ($colonnes as $colonne)
                                            @if ($colonne->enfants->count() > 0)
                                                <th colspan="{{ $colonne->enfants->count() }}" class="text-center">
                                                    {{ $colonne->colonne_ligne }}
                                                </th>
                                            @else
                                                <th rowspan="2" class="text-center">
                                                    {{ $colonne->colonne_ligne }}
                                                </th>
                                            @endif
                                        @endforeach
                                        <th rowspan="2" class="text-center">Total</th>
                                    </tr>
                                </thead>

                                {{-- 🔹 Corps du tableau --}}
                                <tbody>
                                    @foreach ($lignes as $ligne)
                                        <tr>
                                            @if ($ligne->ordre > 1)
                                                <td class="fw-bold">{{ $ligne->nom_ligne }}</td>

                                                @foreach ($colonnes as $colonne)
                                                    @if ($colonne->enfants->count() > 0)
                                                        @foreach ($colonne->enfants as $sousColonne)
                                                            @php
                                                                $cellule = \App\Models\Cellule::where(
                                                                    'ligne_id',
                                                                    $ligne->id,
                                                                )
                                                                    ->where('colonne_id', $sousColonne->id)
                                                                    ->first();

                                                                $valeur = $cellule
                                                                    ? $soumissionsParCellule[$cellule->id]->valeur ??
                                                                        '-'
                                                                    : '-';
                                                            @endphp
                                                            <td class="text-center">{{ $valeur }}</td>
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $cellule = \App\Models\Cellule::where(
                                                                'ligne_id',
                                                                $ligne->id,
                                                            )
                                                                ->where('colonne_id', $colonne->id)
                                                                ->first();

                                                            $valeur = $cellule
                                                                ? $soumissionsParCellule[$cellule->id]->valeur ?? '-'
                                                                : '-';
                                                        @endphp
                                                        <td class="text-center">{{ $valeur }}</td>
                                                    @endif
                                                @endforeach
                                                <td class="fw-bold text-center total-ligne">0.00</td>
                                            @endif
                                        </tr>

                                        {{-- 🔸 Sous-lignes --}}
                                        @foreach ($ligne->enfants as $sousLigne)
                                            <tr class="table-secondary">
                                                <td class="ps-4">{{ $sousLigne->nom_ligne }}</td>
                                                @foreach ($colonnes as $colonne)
                                                    @if ($colonne->enfants->count() > 0)
                                                        @foreach ($colonne->enfants as $sousColonne)
                                                            @php
                                                                $cellule = \App\Models\Cellule::where(
                                                                    'ligne_id',
                                                                    $sousLigne->id,
                                                                )
                                                                    ->where('colonne_id', $sousColonne->id)
                                                                    ->first();

                                                                $valeur = $cellule
                                                                    ? $soumissionsParCellule[$cellule->id]->valeur ??
                                                                        '-'
                                                                    : '-';
                                                            @endphp
                                                            <td class="text-center">{{ $valeur }}</td>
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $cellule = \App\Models\Cellule::where(
                                                                'ligne_id',
                                                                $sousLigne->id,
                                                            )
                                                                ->where('colonne_id', $colonne->id)
                                                                ->first();

                                                            $valeur = $cellule
                                                                ? $soumissionsParCellule[$cellule->id]->valeur ?? '-'
                                                                : '-';
                                                        @endphp
                                                        <td class="text-center">{{ $valeur }}</td>
                                                    @endif
                                                @endforeach
                                                <td class="text-center total-ligne">0.00</td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    {{-- 🔹 Ligne de total général --}}
                                    <tr class="table-primary fw-bold">
                                        <td>Ensemble</td>
                                        @foreach ($colonnes as $colonne)
                                            @if ($colonne->enfants->count() > 0)
                                                @foreach ($colonne->enfants as $sousColonne)
                                                    <td class="text-center total-colonne">0.00</td>
                                                @endforeach
                                            @else
                                                <td class="text-center total-colonne">0.00</td>
                                            @endif
                                        @endforeach
                                        <td class="text-center total-general">0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <h5 class="text-danger">Aucune donnée saisie pour cette assignation.</h5>
                @endif
            </div>
        </div>

        {{-- Modale Rejet Motivé DGESS --}}
        <div class="modal fade" id="modalRejetAssignation" tabindex="-1" aria-labelledby="modalRejetAssignationLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalRejetAssignationLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Rejeter la soumission statistique
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('assignations.rejeter', $assignation->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-muted small">
                                Vous êtes sur le point de rejeter les données soumises par la structure 
                                <strong>{{ $assignation->structure->nom }}</strong>. Un courriel officiel de notification leur sera adressé avec le motif renseigné ci-dessous afin qu'ils puissent corriger et re-soumettre leurs chiffres.
                            </p>

                            <div class="mb-3">
                                <label for="motif_rejet" class="form-label fw-bold text-dark">
                                    Motif du rejet et corrections attendues <span class="text-danger">*</span>
                                </label>
                                <textarea name="motif_rejet" id="motif_rejet" rows="5" class="form-control" 
                                    placeholder="Précisez les cellules ou totaux incohérents, données manquantes ou éléments à corriger..." 
                                    required minlength="10" maxlength="2000"></textarea>
                                <div class="form-text">Minimum 10 caractères. Soyez le plus précis possible pour guider la structure sectorielle.</div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Confirmer le rejet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
