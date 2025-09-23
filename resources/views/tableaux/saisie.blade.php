@extends('layouts.admin')
@section('breadcrumb', 'Tableaux')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Saisie de données: {{ $tableau->libelle }}</h2>
                <p class="text-muted">{{ $tableau->description }}</p>
            </div>
            <div>
                <a href="{{ route('tableaux.show', $tableau->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> Visualiser
                </a>
            </div>
        </div>

        <form action="{{ route('tableaux.save-donnees', $tableau->id) }}" method="POST">
            @csrf

            <div class="row mb-4">
                <div class="col-md-4">
                    
<div class="mb-3">
    <label class="form-label">Structure</label>
    <input type="text" class="form-control" value="{{ $structure->nom }}" disabled>
    <input type="hidden" name="structure_id" value="{{ $structure->id }}">
</div>

                </div>
                <div class="col-md-4">
                    <label for="assignation_id" class="form-label">Version *</label>
                    <select class="form-select" id="assignation_id" name="assignation_id" required>
                        <option value="">Sélectionner une version</option>
                        @foreach ($assignations as $assignation)
                            <option value="{{ $assignation->id }}">
                                {{ $assignation->version }} - {{ $assignation->periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
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
                                        <th rowspan="2" class="align-middle">Libellé</th>
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
                                                                    name="donnees[{{ $cellule->id }}]"
                                                                    style="width: 100px;">
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
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Calcul automatique des totaux
            $('input[type="number"]').on('input', function() {
                calculerTotaux();
            });

            function calculerTotaux() {
                // Calcul des totaux par ligne
                $('tbody tr').each(function() {
                    let total = 0;
                    $(this).find('input[type="number"]').each(function() {
                        let valeur = parseFloat($(this).val()) || 0;
                        total += valeur;
                    });
                    $(this).find('.total-ligne').text(total.toFixed(2));
                });

                // Calcul des totaux par colonne
                let colonneIndex = 0;
                $('.total-colonne').each(function() {
                    let total = 0;
                    $('tbody tr:not(.table-primary)').each(function() {
                        let input = $(this).find('input[type="number"]').eq(colonneIndex);
                        let valeur = parseFloat(input.val()) || 0;
                        total += valeur;
                    });
                    $(this).text(total.toFixed(2));
                    colonneIndex++;
                });

                // Calcul du total général
                let totalGeneral = 0;
                $('.total-colonne').each(function() {
                    totalGeneral += parseFloat($(this).text()) || 0;
                });
                $('.total-general').text(totalGeneral.toFixed(2));
            }
        });
    </script>
@endsection
