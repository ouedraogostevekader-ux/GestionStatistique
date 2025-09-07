{{-- resources/views/programmes/tableau.blade.php --}}
@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('dist/css/tableau_pg_a.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#programmeActiviteModal">
                        <i class="fas fa-plus"></i> Générer Programme d'Activité
                    </button>
                    <button onclick="exportTableToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button onclick="exportTableToPDF()" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Génération Programme d'Activité -->
        <div class="modal fade" id="programmeActiviteModal" tabindex="-1" aria-labelledby="programmeActiviteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="programmeActiviteModalLabel">Générer Programme d'Activité</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('programme-activites.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="libelle" class="form-label">Libellé du Programme d'Activité</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" required>
                            </div>
                            <!-- Programmes sélectionnés (masqués) -->
                            @foreach ($programmes as $programme)
                                <input type="hidden" name="programmes[]" value="{{ $programme->id }}">
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Générer</button>
                        </div>
                    </form>
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
                                                        <td rowspan="{{ $activite->indicateurs->count() }}"
                                                            class="activite">
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
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                                    <h4>Aucun programme budgétaire</h4>
                                    <p>Il n'y a pas encore de programmes budgétaires enregistrés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function exportTableToExcel() {
            window.location.href = '{{ route('programmes.export-excel') }}';
        }

        function exportTableToPDF() {
            window.location.href = '{{ route('programmes.export-pdf') }}';
        }
    </script>
@endsection
