@extends('layouts.admin')
@section('breadcrumb', 'Tableaux')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Données du tableau</li> : {{ $tableau->categorie->libelle }}
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                    <a href="{{ route('tableaux.structure', $tableau->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cogs"></i> Forme
                    </a>
                    @can('assignation-create')
                        <a href="{{ route('tableaux.affecter', $tableau->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-cogs"></i> Assigner le tableau
                        </a>
                    @endcan
                    @can('soumission-create')
                        <a href="{{ route('tableaux.canevas-excel', ['id' => $tableau->id, 'assignation_id' => request('assignation_id')]) }}" class="btn btn-outline-success" title="Télécharger le canevas Excel pré-formaté">
                            <i class="fas fa-file-excel me-1"></i> Canevas Excel
                        </a>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                            <i class="fas fa-file-upload me-1"></i> Importer Excel
                        </button>
                        <a href="{{ route('tableaux.saisie', $tableau->id) }}" class="btn btn-success">
                            <i class="bi bi-edit"></i> Saisir Données
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="container mt-2">
            <h2>{{ $tableau->libelle }}</h2>
        </div>

        @if ($lignes->count() > 0 && $colonnes->count() > 0)

            @foreach ($structures as $structure)
                <div class="card mb-5 mt-2">
                    <div class="card-header bg-light">

                        <h5 class="mb-0">Structure : <strong>{{ $structure->nom }}</strong></h5>
                        <small class="text-muted">{{ $structure->sigle }}</small>

                        {{-- Sélecteur de version --}}
                        <form method="GET" action="{{ route('tableaux.show', $tableau->id) }}" class="mt-3">
                            <input type="hidden" name="structure_id" value="{{ $structure->id }}">
                            <select name="assignation_id" onchange="this.form.submit()"
                                class="form-select w-auto d-inline-block">
                                <option value="">Sélectionner une version</option>
                                @foreach ($assignations as $assignation)
                                    <option value="{{ $assignation->id }}">
                                        {{ $assignation->version->code }} - {{ $assignation->version->annee }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                    </div>

                    @php
                        $assignationId = request('assignation_id');
                        $structureId = request('structure_id');
                        $soumissionKey = $structureId . '_' . $assignationId;
                        $soumissionsParVersion = $soumissions->get($soumissionKey) ?? collect();
                    @endphp

                    @if ($structureId == $structure->id && $assignationId && $soumissionsParVersion->count() > 0)
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            {{-- <th rowspan="2">Libellé</th> --}}
                                            @foreach ($lignes as $ligne)
                                                @if ($ligne->ordre == 1)
                                                    <th>{{ $ligne->nom_ligne }}
                                                    </th>
                                                @endif
                                            @endforeach
                                            @foreach ($colonnes as $colonne)
                                                @if ($colonne->enfants->count() > 0)
                                                    <th colspan="{{ $colonne->enfants->count() }}" class="text-center">
                                                        {{ $colonne->colonne_ligne }}
                                                    </th>
                                                @else
                                                    <th rowspan="2" class="text-center">{{ $colonne->colonne_ligne }}
                                                    </th>
                                                @endif
                                            @endforeach
                                            <th rowspan="2" class="text-center">Total</th>
                                        </tr>
                                        <tr>
                                            @foreach ($colonnes as $colonne)
                                                @foreach ($colonne->enfants as $sousColonne)
                                                    <th class="text-center">{{ $sousColonne->colonne_ligne }}</th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lignes as $ligne)
                                            <tr>
                                                @if ($ligne->ordre != 1)
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
                                                                        ? $soumissionsParVersion->firstWhere(
                                                                            'cellule_id',
                                                                            $cellule->id,
                                                                        )?->valeur
                                                                        : '-';
                                                                @endphp
                                                                <td class="text-center">{{ $valeur ?? '-' }}</td>
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
                                                                    ? $soumissionsParVersion->firstWhere(
                                                                        'cellule_id',
                                                                        $cellule->id,
                                                                    )?->valeur
                                                                    : '-';
                                                            @endphp
                                                            <td class="text-center">{{ $valeur ?? '-' }}</td>
                                                        @endif
                                                    @endforeach
                                                    <td class="total-ligne text-center fw-bold">0.00</td>
                                                @endif
                                            </tr>

                                            {{-- Sous-lignes --}}
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
                                                                        ? $soumissionsParVersion->firstWhere(
                                                                            'cellule_id',
                                                                            $cellule->id,
                                                                        )?->valeur
                                                                        : '-';
                                                                @endphp
                                                                <td class="text-center">{{ $valeur ?? '-' }}</td>
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
                                                                    ? $soumissionsParVersion->firstWhere(
                                                                        'cellule_id',
                                                                        $cellule->id,
                                                                    )?->valeur
                                                                    : '-';
                                                            @endphp
                                                            <td class="text-center">{{ $valeur ?? '-' }}</td>
                                                        @endif
                                                    @endforeach
                                                    <td class="total-ligne text-center">0.00</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        {{-- Ligne Ensemble (Total) --}}
                                        <tr class="table-primary fw-bold">
                                            <td>Ensemble</td>
                                            @foreach ($colonnes as $colonne)
                                                @if ($colonne->enfants->count() > 0)
                                                    @foreach ($colonne->enfants as $sousColonne)
                                                        <td class="text-center">
                                                            <span class="total-colonne">0.00</span>
                                                        </td>
                                                    @endforeach
                                                @else
                                                    <td class="text-center">
                                                        <span class="total-colonne">0.00</span>
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td class="text-center">
                                                <span class="total-general">0.00</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="text-muted">{{ $tableau->description ?? '' }}</p>
                            </div>
                        </div>
                    @elseif($structureId == $structure->id && $assignationId)
                        <div class="card-body">
                            <div class="alert alert-warning">
                                Aucune donnée saisie pour cette version.
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <h5>Tableau vide</h5>
                <p>Ce tableau n'a pas encore de structure définie.</p>
            </div>
        @endif
    </div>
    @include('tableaux.modals.import-excel')
@endsection
