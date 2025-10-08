@extends('layouts.admin')
@section('breadcrumb', 'Tableaux')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Saisie de données</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="mt-4 d-flex justify-content-end">
                    <a class="btn btn-outline-primary" href="{{ route('tableaux.show', $tableau->id) }}">
                        <i class="fas fa-eye"></i> Visualiser
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>{{ $tableau->libelle }}</h2>
    </div>

    <form action="{{ route('tableaux.save-donnees', $tableau->id) }}" method="POST">
        @csrf
        <div class="container row mb-4">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Structure</label>
                    <input type="text" class="form-control" value="{{ $structure->nom }}" disabled>
                    <input type="hidden" name="structure_id" value="{{ $structure->id }}">
                </div>
            </div>
            <div class="col-md-4">
                <label for="assignation_id" class="form-label">Version <span id="colorant">*</span></label>
                <select class="form-select" id="assignation_id" name="assignation_id" required>
                    <option value="">Sélectionner une version</option>
                    @foreach ($assignations as $assignation)
                        <option value="{{ $assignation->id }}">
                            {{ $assignation->version->code }} - {{ $assignation->version->annee }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($lignes->count() > 0 && $colonnes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    {{-- <th rowspan="2" class="align-middle">Libellé</th> --}}
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
                                            <th rowspan="2" class="align-middle text-center">
                                                {{ $colonne->colonne_ligne }}
                                            </th>
                                        @endif
                                    @endforeach
                                    <th rowspan="2" class="align-middle text-center">Total</th>
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
                                                        <td class="text-center">
                                                            @php
                                                                $cellule = \App\Models\Cellule::where(
                                                                    'ligne_id',
                                                                    $ligne->id,
                                                                )
                                                                    ->where('colonne_id', $sousColonne->id)
                                                                    ->first();
                                                            @endphp
                                                            @if ($cellule)
                                                                <input type="number" step="0.01"
                                                                    class="form-control form-control-sm text-center"
                                                                    name="donnees[{{ $cellule->id }}]"
                                                                    style="width: 100px;">
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @else
                                                    <td class="text-center">
                                                        @php
                                                            $cellule = \App\Models\Cellule::where(
                                                                'ligne_id',
                                                                $ligne->id,
                                                            )
                                                                ->where('colonne_id', $colonne->id)
                                                                ->first();
                                                        @endphp
                                                        @if ($cellule)
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm text-center"
                                                                name="donnees[{{ $cellule->id }}]" style="width: 100px;">
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td class="text-center">
                                                <span class="total-ligne fw-bold">0.00</span>
                                            </td>
                                        @endif
                                    </tr>

                                    {{-- Afficher les sous-lignes --}}
                                    @foreach ($ligne->enfants as $sousLigne)
                                        <tr class="table-secondary">
                                            <td class="ps-4">{{ $sousLigne->nom_ligne }}</td>
                                            @foreach ($colonnes as $colonne)
                                                @if ($colonne->enfants->count() > 0)
                                                    @foreach ($colonne->enfants as $sousColonne)
                                                        <td class="text-center">
                                                            @php
                                                                $cellule = \App\Models\Cellule::where(
                                                                    'ligne_id',
                                                                    $sousLigne->id,
                                                                )
                                                                    ->where('colonne_id', $sousColonne->id)
                                                                    ->first();
                                                            @endphp
                                                            @if ($cellule)
                                                                <input type="number" step="0.01"
                                                                    class="form-control form-control-sm text-center"
                                                                    name="donnees[{{ $cellule->id }}]"
                                                                    style="width: 100px;">
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @else
                                                    <td class="text-center">
                                                        @php
                                                            $cellule = \App\Models\Cellule::where(
                                                                'ligne_id',
                                                                $sousLigne->id,
                                                            )
                                                                ->where('colonne_id', $colonne->id)
                                                                ->first();
                                                        @endphp
                                                        @if ($cellule)
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm text-center"
                                                                name="donnees[{{ $cellule->id }}]" style="width: 100px;">
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td class="text-center">
                                                <span class="total-ligne">0.00</span>
                                            </td>
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
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h5>Structure incomplète</h5>
                        <p>Ce tableau n'a pas de structure complète définie.</p>
                        <a href="{{ route('tableaux.structure', $tableau->id) }}" class="btn btn-primary">
                            Définir la structure
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </form>
    <p class="text-muted mt-2">{{ $tableau->description }}</p>
    </div>
@endsection
