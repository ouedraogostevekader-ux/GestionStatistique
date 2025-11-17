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
                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('assignations.dashboard') }}" class="btn btn-outline-secondary">
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
                                <span class="badge bg-success">Soumise</span>
                            @break

                            @case(3)
                                <span class="badge bg-danger">Expirée</span>
                            @break

                            @default
                                <span class="badge bg-light text-dark">Inconnu</span>
                        @endswitch
                    </div>
                </div>
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

                @if ($assignation->statut == 2)
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
    @endsection
