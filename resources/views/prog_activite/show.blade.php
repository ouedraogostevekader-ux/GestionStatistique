{{-- resources/views/programmes/tableau.blade.php --}}
@extends('layouts.admin')

{{-- @section('title', 'Tableau Programme Budgétaire') --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('dist/css/show_pgb_table.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="btn-group">

                    {{-- <button onclick="exportTableToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button> --}}
                    @can('programme_Bugetaire-validate')
                        @if ($programme->statut == 0)
                            <span class="ml-3 text-warning">Votre programme n'est pas encore validé</span>
                            <button id="ValidationModal" type="button" class="btn btn-outline-success ml-3"
                                title="Valider le programme budgetaire" data-bs-toggle="modal"
                                data-bs-target="#ValidationModal">
                                <i class="bi bi-check"></i> Valider ce programme
                            </button>
                        @else
                            <div class="alert alert-info">Votre programme est déjà validé</div>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="programme-table" id="programmeTable">
                <thead>
                    <tr>
                        <th rowspan="2">Objectifs/Activités</th>
                        <th colspan="4">Indicateurs</th>
                        <th rowspan="2">Coûts Estimatifs<br>(en FCFA)</th>
                        <th colspan="4">Période de réalisation</th>
                    </tr>
                    <tr>
                        <th>Indicateurs</th>
                        <th>Unité de l'indicateur</th>
                        <th>Référence</th>
                        <th>Cible</th>
                        <th>T1</th>
                        <th>T2</th>
                        <th>T3</th>
                        <th>T4</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- En-tête programme -->
                    <tr>
                        <td colspan="10" class="programme-header">
                            Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                        </td>
                    </tr>

                    @foreach ($programme->objectifsStrategiques as $objIndex => $objectif)
                        <!-- Objectif stratégique -->
                        @if ($objectif->indicateurs->count() > 0)
                            @foreach ($objectif->indicateurs as $indIndex => $indicateur)
                                <tr>
                                    @if ($indIndex == 0)
                                        <td rowspan="{{ $objectif->indicateurs->count() }}" class="objectif-strategique">
                                            {{ $objectif->libelle }}
                                        </td>
                                    @endif
                                    <td>{{ $indicateur->libelle }}</td>
                                    <td>{{ $indicateur->unite_mesure }}</td>
                                    <td>{{ $indicateur->valeur_ref }}</td>
                                    <td>{{ $indicateur->valeur_cible }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="objectif-strategique">
                                    {{ $objectif->libelle }}
                                </td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endif

                        <!-- Actions -->
                        @foreach ($objectif->actions as $actionIndex => $action)
                            <tr>
                                <td class="action">
                                    {{ $action->libelle }}
                                </td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <!-- Objectifs spécifiques -->
                            @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                <!-- Indicateurs objectif spécifique -->
                                @if ($objSpec->indicateurs->count() > 0)
                                    @foreach ($objSpec->indicateurs as $indIndex => $indicateur)
                                        <tr>
                                            @if ($indIndex == 0)
                                                <td rowspan="{{ $objSpec->indicateurs->count() }}"
                                                    class="objectif-specifique">
                                                    {{ $objSpec->libelle }}
                                                </td>
                                            @endif
                                            <td>{{ $indicateur->libelle }}</td>
                                            <td>{{ $indicateur->unite_mesure }}</td>
                                            <td>{{ $indicateur->valeur_ref }}</td>
                                            <td>{{ $indicateur->valeur_cible }}</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="objectif-specifique">
                                            {{ $objSpec->libelle }}
                                        </td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                @endif

                                <!-- Activités -->
                                @foreach ($objSpec->activites as $activiteIndex => $activite)
                                    @if ($activite->indicateurs->count() > 0)
                                        @foreach ($activite->indicateurs as $indIndex => $indicateur)
                                            <tr>
                                                @if ($indIndex == 0)
                                                    <td rowspan="{{ $activite->indicateurs->count() }}" class="activite">
                                                        {{ $activite->libelle }}
                                                    </td>
                                                @endif
                                                <td>{{ $indicateur->libelle }}</td>
                                                <td>{{ $indicateur->unite_mesure }}</td>
                                                <td>{{ $indicateur->valeur_ref }}</td>
                                                <td>{{ $indicateur->valeur_cible }}</td>
                                                @if ($indIndex == 0)
                                                    <td rowspan="{{ $activite->indicateurs->count() }}">
                                                        {{ number_format($activite->cout_previsionnel) }}
                                                    </td>
                                                    @php
                                                        $periodes = $activite->periodes->pluck('libelle')->toArray();
                                                    @endphp
                                                    <td rowspan="{{ $activite->indicateurs->count() }}">
                                                        @if (in_array('T1', $periodes) || in_array('Trimestre 1', $periodes))
                                                            <span class="periode-check">✓</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td rowspan="{{ $activite->indicateurs->count() }}">
                                                        @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                            <span class="periode-check">✓</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td rowspan="{{ $activite->indicateurs->count() }}">
                                                        @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                            <span class="periode-check">✓</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td rowspan="{{ $activite->indicateurs->count() }}">
                                                        @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                            <span class="periode-check">✓</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="activite">
                                                {{ $activite->libelle }}
                                            </td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>{{ number_format($activite->cout_previsionnel) }}</td>
                                            @php
                                                $periodes = $activite->periodes->pluck('libelle')->toArray();
                                            @endphp
                                            <td>
                                                @if (in_array('T1', $periodes) || in_array('Trimestre 1', $periodes))
                                                    <span class="periode-check">✓</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                    <span class="periode-check">✓</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                    <span class="periode-check">✓</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                    <span class="periode-check">✓</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
{{-- Valider un programme --}}
<div class="modal fade" id="ValidationModal" tabindex="-1" aria-labelledby="ValidationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ValidationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="validateModalMessage">
                <p class="alert alert-warning">Vous souhaitez valider ce programme budgétaire en tant que responsable ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('programmebudgetaire.validate', $programme->id) }}" class="btn btn-warning"
                    id="confirmLockBtn">Confirmer</a>
            </div>
        </div>
    </div>
</div>
