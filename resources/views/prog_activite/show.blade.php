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
                                            OS : {{ $objectif->libelle }}
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
                                    OS : {{ $objectif->libelle }}
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
                                    Action : {{ $action->libelle }}
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
                                                    OSp :
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
                                            OSp :
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
                                                        Act: {{ $activite->libelle }}
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
                                                Act: {{ $activite->libelle }}
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
