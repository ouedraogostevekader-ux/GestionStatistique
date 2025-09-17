<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title> Programme d'activité </title>
    <link rel="stylesheet" href="{{ public_path('dist/css/prog_activite_pdf.css') }}">
</head>

<body>

    <!-- En-tête -->
    <div class="row d-flex justify-content-center" style="margin-bottom: 250px; font-size: 14px;">
        <div class="col-sm-5" style="text-align: center;float: left;">

            MINISTERE DES INFRASTRUCTURES<br>
            ET DU DESENCLAVEMENT<br>
            ------------------- <br>
            SÉCRETARIAT GÉNÉRAL<br>
            ------------------- <br>
            DIRECTION GÉNÉRALE DES ETUDES<br> STATISTIQUES ET SECTORIELLES <br>
        </div>
        <div class="col-sm-2 px-3" style="text-align: center; float: center; margin-left: 20%;">
            <img src="{{ public_path('dist/assets/img/ARMOIRIES-BF.jpg') }}" alt="Logo ARMOIRIES" width="18%" /><br />
        </div>
        <div class="col-sm-5" style="text-align: center;float: right;">
            BURKINA FASO <br>
            --------------<br>
            La Patrie ou la Mort, nous Vaincrons <br>
            <br>
            {{--  @if (isset($qrcode))
                <img src="data:image/png;base64,{{ $qrcode }}">
            @endif --}}
        </div>
    </div>
    <h3 id="titre" style="text-align: center; text-justify: distribute
    ; text-decoration: underline;">Programme
        d'activités du
        {{ date('d/m/Y, H:i') }}</h3>
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="btn-group">

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
                                                                <span class="periode-check">X</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $activite->indicateurs->count() }}">
                                                            @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                                <span class="periode-check">X</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $activite->indicateurs->count() }}">
                                                            @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                                <span class="periode-check">X</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $activite->indicateurs->count() }}">
                                                            @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                                <span class="periode-check">X</span>
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
                                                        <span class="periode-check">X</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                        <span class="periode-check">X</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                        <span class="periode-check">X</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                        <span class="periode-check">X</span>
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
</body>

</html>
