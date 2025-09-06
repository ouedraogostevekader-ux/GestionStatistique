{{-- resources/views/programmes/tableau.blade.php --}}
@extends('layouts.admin')

{{-- @section('title', 'Tableau Programme Budgétaire') --}}
@section('styles')
    <style>
        .programme-table {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #343a40;
        }

        .programme-table th,
        .programme-table td {
            border: 1px solid #6c757d;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .programme-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: 1px solid #0056b3;
        }

        .programme-header {
            background-color: #28a745 !important;
            color: white;
            font-weight: bold;
            text-align: left;
            border: 2px solid #1e7e34 !important;
        }

        .objectif-strategique {
            background-color: #f8f9fa;
            text-align: left;
            font-weight: bold;
            border-left: 3px solid #007bff !important;
        }

        .action {
            background-color: #e9ecef;
            text-align: left;
            font-weight: bold;
            padding-left: 20px;
            border-left: 3px solid #28a745 !important;
        }

        .objectif-specifique {
            background-color: #f1f2f6;
            text-align: left;
            padding-left: 40px;
            border-left: 3px solid #ffc107 !important;
        }

        .activite {
            text-align: left;
            padding-left: 60px;
            border-left: 3px solid #fd7e14 !important;
        }

        .programme-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .programme-table .programme-header:hover {
            background-color: #28a745 !important;
        }

        .periode-check {
            color: #28a745;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            .programme-table {
                font-size: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="btn-group">
                    {{-- <a href="{{ route('programme-activites.show', $programme->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a> --}}
                    {{-- <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </button> --}}
                    <button onclick="exportTableToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
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
                    @forelse($programmes as $programme)
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
                                            <td rowspan="{{ $objectif->indicateurs->count() }}"
                                                class="objectif-strategique">
                                                OS{{ $objIndex + 1 }} : {{ $objectif->libelle }}
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
                                        OS{{ $objIndex + 1 }} : {{ $objectif->libelle }}
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
                                        Action {{ $objIndex + 1 }}.{{ $actionIndex + 1 }} : {{ $action->libelle }}
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
                                                        OSp
                                                        {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}
                                                        :
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
                                                OSp {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }} :
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
                                                        <td rowspan="{{ $activite->indicateurs->count() }}"
                                                            class="activite">
                                                            Act
                                                            {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}.{{ $activiteIndex + 1 }}
                                                            : {{ $activite->libelle }}
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
                                                            $periodes = $activite->periodes
                                                                ->pluck('libelle')
                                                                ->toArray();
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
                                                    Act
                                                    {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}.{{ $activiteIndex + 1 }}
                                                    : {{ $activite->libelle }}
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
                    @empty
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h4>Aucun programme budgétaire</h4>
                            <p>Il n'y a pas encore de programmes budgétaires enregistrés.</p>
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function exportTableToExcel() {
            const table = document.getElementById('programmeTable');
            const ws = XLSX.utils.table_to_sheet(table);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Programme");
            XLSX.writeFile(wb, `Programme_${document.querySelector('.programme-header').textContent.trim()}.xlsx`);
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection

{{-- resources/views/programmes/tableau.blade.php --}}
{{-- @extends('layouts.admin')

@section('styles')
    <style>
        .programme-table {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #343a40;
        }

        .programme-table th,
        .programme-table td {
            border: 1px solid #6c757d;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .programme-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: 1px solid #0056b3;
        }

        .accordion-button {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: bold;
            border: none !important;
            padding: 15px 20px !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #1e7e34 !important;
            color: white !important;
        }

        .accordion-button:focus {
            box-shadow: none !important;
        }

        .objectif-strategique-btn {
            background-color: #007bff !important;
            color: white !important;
            font-weight: bold;
            padding: 12px 20px !important;
        }

        .objectif-strategique-btn:not(.collapsed) {
            background-color: #0056b3 !important;
        }

        .action-btn {
            background-color: #28a745 !important;
            color: white !important;
            padding: 10px 20px !important;
            margin-left: 20px;
        }

        .action-btn:not(.collapsed) {
            background-color: #1e7e34 !important;
        }

        .objectif-specifique-btn {
            background-color: #ffc107 !important;
            color: #212529 !important;
            padding: 8px 20px !important;
            margin-left: 40px;
        }

        .objectif-specifique-btn:not(.collapsed) {
            background-color: #e0a800 !important;
        }

        .activite-header {
            background-color: #fd7e14;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            margin-left: 60px;
        }

        .table-container {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .periode-check {
            color: #28a745;
            font-weight: bold;
        }

        .no-border {
            border: none !important;
        }

        @media print {
            .no-print {
                display: none;
            }

            .accordion {
                display: none;
            }

            .print-table {
                display: block !important;
            }
        }

        .print-table {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <button onclick="exportTableToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Version Accordéon pour l'affichage -->
        <div class="accordion no-print" id="programmeAccordion">
            <!-- Programme Principal -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="programmeHeader">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#programmeContent" aria-expanded="true" aria-controls="programmeContent">
                        Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                    </button>
                </h2>
                <div id="programmeContent" class="accordion-collapse collapse show" aria-labelledby="programmeHeader"
                    data-bs-parent="#programmeAccordion">
                    <div class="accordion-body">

                        <!-- Objectifs Stratégiques -->
                        @foreach ($programme->objectifsStrategiques as $objIndex => $objectif)
                            <div class="accordion" id="objectifAccordion{{ $objIndex }}">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="objectifHeader{{ $objIndex }}">
                                        <button class="accordion-button collapsed objectif-strategique-btn" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#objectifContent{{ $objIndex }}"
                                            aria-expanded="false" aria-controls="objectifContent{{ $objIndex }}">
                                            OS{{ $objIndex + 1 }} : {{ $objectif->libelle }}
                                        </button>
                                    </h2>
                                    <div id="objectifContent{{ $objIndex }}" class="accordion-collapse collapse"
                                        aria-labelledby="objectifHeader{{ $objIndex }}"
                                        data-bs-parent="#objectifAccordion{{ $objIndex }}">
                                        <div class="accordion-body">

                                            <!-- Indicateurs de l'objectif stratégique -->
                                            @if ($objectif->indicateurs->count() > 0)
                                                <div class="table-container">
                                                    <h6>Indicateurs de l'Objectif Stratégique</h6>
                                                    <table class="table table-sm">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>Indicateur</th>
                                                                <th>Unité</th>
                                                                <th>Référence</th>
                                                                <th>Cible</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($objectif->indicateurs as $indicateur)
                                                                <tr>
                                                                    <td>{{ $indicateur->libelle }}</td>
                                                                    <td>{{ $indicateur->unite_mesure }}</td>
                                                                    <td>{{ $indicateur->valeur_ref }}</td>
                                                                    <td>{{ $indicateur->valeur_cible }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif

                                            <!-- Actions -->
                                            @foreach ($objectif->actions as $actionIndex => $action)
                                                <div class="accordion mt-3"
                                                    id="actionAccordion{{ $objIndex }}_{{ $actionIndex }}">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="actionHeader{{ $objIndex }}_{{ $actionIndex }}">
                                                            <button class="accordion-button collapsed action-btn"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#actionContent{{ $objIndex }}_{{ $actionIndex }}"
                                                                aria-expanded="false"
                                                                aria-controls="actionContent{{ $objIndex }}_{{ $actionIndex }}">
                                                                Action {{ $objIndex + 1 }}.{{ $actionIndex + 1 }} :
                                                                {{ $action->libelle }}
                                                            </button>
                                                        </h2>
                                                        <div id="actionContent{{ $objIndex }}_{{ $actionIndex }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="actionHeader{{ $objIndex }}_{{ $actionIndex }}"
                                                            data-bs-parent="#actionAccordion{{ $objIndex }}_{{ $actionIndex }}">
                                                            <div class="accordion-body">

                                                                <!-- Objectifs Spécifiques -->
                                                                @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                                                    <div class="accordion mt-2"
                                                                        id="objSpecAccordion{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header"
                                                                                id="objSpecHeader{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                <button
                                                                                    class="accordion-button collapsed objectif-specifique-btn"
                                                                                    type="button" data-bs-toggle="collapse"
                                                                                    data-bs-target="#objSpecContent{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="objSpecContent{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                    OSp
                                                                                    {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}
                                                                                    : {{ $objSpec->libelle }}
                                                                                </button>
                                                                            </h2>
                                                                            <div id="objSpecContent{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                class="accordion-collapse collapse"
                                                                                aria-labelledby="objSpecHeader{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                data-bs-parent="#objSpecAccordion{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                <div class="accordion-body">

                                                                                    <!-- Indicateurs objectif spécifique -->
                                                                                    @if ($objSpec->indicateurs->count() > 0)
                                                                                        <div class="table-container">
                                                                                            <h6>Indicateurs de l'Objectif
                                                                                                Spécifique</h6>
                                                                                            <table class="table table-sm">
                                                                                                <thead
                                                                                                    class="table-warning">
                                                                                                    <tr>
                                                                                                        <th>Indicateur</th>
                                                                                                        <th>Unité</th>
                                                                                                        <th>Référence</th>
                                                                                                        <th>Cible</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    @foreach ($objSpec->indicateurs as $indicateur)
                                                                                                        <tr>
                                                                                                            <td>{{ $indicateur->libelle }}
                                                                                                            </td>
                                                                                                            <td>{{ $indicateur->unite_mesure }}
                                                                                                            </td>
                                                                                                            <td>{{ $indicateur->valeur_ref }}
                                                                                                            </td>
                                                                                                            <td>{{ $indicateur->valeur_cible }}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    @endif

                                                                                    <!-- Activités -->
                                                                                    @foreach ($objSpec->activites as $activiteIndex => $activite)
                                                                                        <div class="activite-header mt-3">
                                                                                            Act
                                                                                            {{ $objIndex + 1 }}.{{ $actionIndex + 1 }}.{{ $objSpecIndex + 1 }}.{{ $activiteIndex + 1 }}
                                                                                            : {{ $activite->libelle }}
                                                                                        </div>

                                                                                        <div class="table-container">
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    <strong>Coût
                                                                                                        Prévisionnel:</strong>
                                                                                                    {{ number_format($activite->cout_previsionnel) }}
                                                                                                    FCFA
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <strong>Périodes de
                                                                                                        réalisation:</strong>
                                                                                                    @php
                                                                                                        $periodes = $activite->periodes
                                                                                                            ->pluck(
                                                                                                                'libelle',
                                                                                                            )
                                                                                                            ->toArray();
                                                                                                    @endphp
                                                                                                    <span class="ms-2">
                                                                                                        T1: @if (in_array('T1', $periodes) || in_array('Trimestre 1', $periodes))
                                                                                                            <span
                                                                                                            class="periode-check">✓</span>@else-
                                                                                                        @endif |
                                                                                                        T2: @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                                                                            <span
                                                                                                            class="periode-check">✓</span>@else-
                                                                                                        @endif |
                                                                                                        T3: @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                                                                            <span
                                                                                                            class="periode-check">✓</span>@else-
                                                                                                        @endif |
                                                                                                        T4: @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                                                                            <span
                                                                                                            class="periode-check">✓</span>@else-
                                                                                                        @endif
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>

                                                                                            @if ($activite->indicateurs->count() > 0)
                                                                                                <h6 class="mt-3">
                                                                                                    Indicateurs de
                                                                                                    l'Activité</h6>
                                                                                                <table
                                                                                                    class="table table-sm">
                                                                                                    <thead
                                                                                                        class="table-secondary">
                                                                                                        <tr>
                                                                                                            <th>Indicateur
                                                                                                            </th>
                                                                                                            <th>Unité</th>
                                                                                                            <th>Référence
                                                                                                            </th>
                                                                                                            <th>Cible</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($activite->indicateurs as $indicateur)
                                                                                                            <tr>
                                                                                                                <td>{{ $indicateur->libelle }}
                                                                                                                </td>
                                                                                                                <td>{{ $indicateur->unite_mesure }}
                                                                                                                </td>
                                                                                                                <td>{{ $indicateur->valeur_ref }}
                                                                                                                </td>
                                                                                                                <td>{{ $indicateur->valeur_cible }}
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Version tableau pour l'impression (masquée par défaut) -->
        <div class="print-table">
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
                    <!-- Votre code de tableau original ici pour l'impression -->
                    <!-- En-tête programme -->
                    <tr>
                        <td colspan="10" class="programme-header">
                            Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                        </td>
                    </tr>

                    @foreach ($programme->objectifsStrategiques as $objIndex => $objectif)
                        <!-- Le reste du code original du tableau ici -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function exportTableToExcel() {
            // Générer dynamiquement le tableau pour l'export
            generateFullTable();
            const table = document.getElementById('programmeTable');
            const ws = XLSX.utils.table_to_sheet(table);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Programme");
            XLSX.writeFile(wb, `Programme_${document.querySelector('.accordion-button').textContent.trim()}.xlsx`);
        }

        function generateFullTable() {
            // Cette fonction peut reconstruire le tableau complet à partir des données de l'accordéon
            // pour l'export Excel si nécessaire
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection --}}
