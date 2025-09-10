<!DOCTYPE html>
<html>

<head>
    @php
        $periodeModel = App\Models\Periode::find($periode);
        $tableauSommeProgramme = [];
        $tableauSommeAction = [];
    @endphp
    <meta charset="utf-8">
    <title>Rapport d'activités de {{ $periodeModel->libelle ?? 'Indisponible' }} {{ $annee }}</title>
    <style>
        /* body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            margin: 10px;
        } */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #7CB342;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .programme-title {
            background-color: #E8F5E8;
            font-weight: bold;
            text-align: center;
        }

        .objectif-strategique {
            background-color: #F0F8FF;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .action-title {
            background-color: #e8f5f5;
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .middle {
            vertical-align: middle;
        }
    </style>
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
        </div>
    </div>

    <h3>Rapport d'activités de {{ $periodeModel->libelle ?? 'Indisponible' }} {{ $periode }} {{ $annee }}
        {{ $date->format('d-m-Y') }} généré le {{ now()->format('d/m/Y à H:i:s') }} </h3>
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Objectifs/Activités</th>
                <th style="width: 15%;">Indicateurs de Résultats</th>
                <th style="width: 8%;">Unité de l'indicateur</th>
                <th style="width: 8%;">Résultats attendus</th>
                <th style="width: 8%;">Résultats Atteints</th>
                <th style="width: 8%;">Taux de réalisation (%)</th>
                <th style="width: 10%;">Coûts effectifs (en milliers de FCFA)</th>
                <th style="width: 18%;">Observations</th>
            </tr>
        </thead>
        <tbody>

            <!-- Autres lignes... -->
            @foreach ($data as $programme)
                <tr>
                    <td colspan="8" class="programme-title">
                        Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                        @php
                            $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle] = [
                                'nombre' => 0,
                                'achevees' => 0,
                                'en_cours' => 0,
                                'non_demarrees' => 0,
                                'taux_realisation' => 0,
                                'cout_effectif' => 0,
                            ];
                        @endphp
                    </td>
                </tr>
                <!-- Objectif stratégique -->
                @foreach ($programme->objectifsStrategiques as $objectifStrategique)
                    @if ($objectifStrategique->indicateurs->count() > 0)
                        <tr>
                            <td rowspan="{{ $objectifStrategique->indicateurs->count() }}"
                                class="objectif-strategique">
                                Objectif stratégique {{ $objectifStrategique->libelle }}
                            </td>
                            @php
                                // Obtenir le premier indicateur de l'objectif stratégique
$firstIndicateur = $objectifStrategique->indicateurs->first();
$lastSuivi = $firstIndicateur
    ? $firstIndicateur->suivis()->where('periode', $periode)->first()
                                    : null;
                                $div =
                                    floatval($firstIndicateur->valeur_cible ?? 0) -
                                    floatval($firstIndicateur->valeur_ref ?? 0);
                                $div = $div != 0 ? $div : 1;
                                $taux =
                                    ((floatval($lastSuivi->resultat_atteint ?? 0) -
                                        floatval($firstIndicateur->valeur_ref ?? 0)) *
                                        100) /
                                    $div;
                            @endphp
                            <td class="center">{{ $firstIndicateur->libelle ?? 'N/A' }}</td>
                            <td class="center">{{ $firstIndicateur->unite_mesure ?? 'N/A' }}</td>
                            <td class="center">{{ $firstIndicateur->valeur_cible ?? 'N/A' }}</td>
                            <td class="center">{{ $lastSuivi->resultat_atteint ?? 'N/E' }}</td>
                            <td class="center">
                                @if (isset($lastSuiv) && is_numeric($firstIndicateur->valeur_cible))
                                    {{ number_format($taux, 2, ',', ' ') }} %
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="center">{{ $lastSuivi->cout_effectif ?? '-' }}</td>
                            <td class="center">{{ $lastSuivi->observations ?? '-' }}</td>
                        </tr>
                        @foreach ($objectifStrategique->indicateurs as $indicateur)
                            @if ($indicateur->id != $firstIndicateur->id)
                                @php
                                    $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();

                                    $div =
                                        floatval($indicateur->valeur_cible ?? 0) -
                                        floatval($indicateur->valeur_ref ?? 0);
                                    $div = $div != 0 ? $div : 1;
                                    $taux =
                                        ((floatval($lastSuiv->resultat_atteint ?? 0) -
                                            floatval($indicateur->valeur_ref ?? 0)) *
                                            100) /
                                        $div;
                                @endphp
                                <tr>
                                    <td class="center">{{ $indicateur->libelle }}</td>
                                    <td class="center">{{ $indicateur->unite_mesure }}</td>
                                    <td class="center">{{ $indicateur->valeur_cible }}</td>
                                    <td class="center">{{ $lastSuiv->resultat_atteint ?? 'N/E' }}</td>
                                    <td class="center">
                                        @if (isset($lastSuiv) && is_numeric($indicateur->valeur_cible))
                                            {{ number_format($taux, 2, ',', ' ') }} %
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="center">{{ $lastSuiv->cout_effectif ?? '-' }}</td>
                                    <td class="center">{{ $lastSuiv->observations ?? '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <!-- Action -->
                        @foreach ($objectifStrategique->actions()->orderBy('libelle')->get() as $action)
                            <tr>
                                <td colspan="8" class="action-title">
                                    Action {{ $action->libelle }}
                                    @php
                                        $tableauSommeAction[$action->libelle] = [
                                            'nombre' => 0,
                                            'achevees' => 0,
                                            'en_cours' => 0,
                                            'non_demarrees' => 0,
                                            'taux_realisation' => 0,
                                            'cout_effectif' => 0,
                                        ];
                                    @endphp
                                </td>
                            </tr>
                            <!-- Objectif spécifique -->
                            @foreach ($action->objectifsSpecifiques()->orderBy('libelle')->get() as $objectifsSpecifique)
                                @if ($objectifsSpecifique->indicateursProgrammes->count() > 0)
                                    <tr>
                                        <td rowspan="{{ $objectifsSpecifique->indicateursProgrammes->count() }}"
                                            class="center middle objectif-strategique">
                                            Objectif spécifique {{ $objectifsSpecifique->libelle }}
                                        </td>
                                        @php
                                            // Obtenir le premier indicateur de l'objectif opérationnel
$firstIndi = $objectifsSpecifique->indicateursProgrammes->first();
$lastSuiv = $firstIndi
    ? $firstIndi->suivis()->where('periode', $periode)->first()
                                                : null;
                                            $div =
                                                floatval($firstIndi->valeur_cible ?? 0) -
                                                floatval($firstIndi->valeur_ref ?? 0);
                                            $div = $div != 0 ? $div : 1;
                                            $taux =
                                                ((floatval($lastSuiv->resultat_atteint ?? 0) -
                                                    floatval($firstIndi->valeur_ref ?? 0)) *
                                                    100) /
                                                $div;
                                        @endphp
                                        <td class="center">{{ $firstIndi->libelle ?? 'N/A' }}</td>
                                        <td class="center">{{ $firstIndi->unite_mesure ?? 'N/A' }}</td>
                                        <td class="center">{{ $firstIndi->valeur_cible ?? 'N/A' }}</td>
                                        <td class="center">{{ $lastSuiv->resultat_atteint ?? 'N/E' }}</td>
                                        <td class="center">
                                            @if (isset($lastSuiv) && is_numeric($firstIndi->valeur_cible))
                                                {{ number_format($taux, 2, ',', ' ') }} %
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="center">{{ $lastSuiv->cout_effectif ?? '-' }}</td>
                                        <td class="center">{{ $lastSuiv->observations ?? '-' }}</td>
                                    </tr>
                                    @foreach ($objectifsSpecifique->indicateursProgrammes as $indicateur)
                                        @if ($indicateur->id != $firstIndi->id)
                                            @php
                                                $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                                                $div =
                                                    floatval($indicateur->valeur_cible ?? 0) -
                                                    floatval($indicateur->valeur_ref ?? 0);
                                                $div = $div != 0 ? $div : 1;
                                                $taux =
                                                    ((floatval($lastSuiv->resultat_atteint ?? 0) -
                                                        floatval($indicateur->valeur_ref ?? 0)) *
                                                        100) /
                                                    $div;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $indicateur->libelle }}</td>
                                                <td class="center">{{ $indicateur->unite_mesure }}</td>
                                                <td class="center">{{ $indicateur->valeur_cible }}</td>
                                                <td class="center">{{ $lastSuiv->resultat_atteint ?? 'N/E' }}</td>
                                                <td class="center">
                                                    @if (isset($lastSuiv) && is_numeric($indicateur->valeur_cible))
                                                        {{ number_format($taux, 2, ',', ' ') }} %
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="center">{{ $lastSuiv->cout_effectif ?? '-' }}</td>
                                                <td class="center">{{ $lastSuiv->observations ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <!-- Activité -->
                                    @foreach ($objectifsSpecifique->activites()->orderBy('libelle')->get() as $activite)
                                        @if ($activite->indicateursProgrammes->count() > 0)
                                            <tr>
                                                <td>
                                                    Activité {{ $activite->libelle }}
                                                </td>
                                                @php
                                                    // Obtenir le premier indicateur de l'activité
$firstI = $activite->indicateursProgrammes->first();
$lastS = $firstI
    ? $firstI->suivis()->where('periode', $periode)->first()
    : null;
if ($periodeModel->libelle == 'T4') {
    // recuperer le dernier suivis ordonné par le libellé de la relation période du suivi
    $lastS = $firstI
        ? $firstI
            ->suivis()
            ->join('periodes', 'suivis.periode', '=', 'periodes.id')
            ->orderBy('periodes.libelle', 'desc')
            ->first()
        : null;
}

// Si l'activite a un suivi dans la periode
                                                    if (isset($lastS)) {
                                                        $tableauSommeAction[$action->libelle]['nombre'] += 1;
                                                        // calcul du taux de realisation = (atteint-reference)/(cible-reference)
                                                        $div =
                                                            floatval($firstI->valeur_cible) -
                                                            floatval($firstI->valeur_ref);
                                                        $div = $div != 0 ? $div : 1;
                                                        $taux =
                                                            ((floatval($lastS->resultat_atteint) -
                                                                floatval($firstI->valeur_ref)) *
                                                                100) /
                                                            $div;
                                                        $tableauSommeAction[$action->libelle][
                                                            'taux_realisation'
                                                        ] += $taux;

                                                        $tableauSommeAction[$action->libelle][
                                                            'cout_effectif'
                                                        ] += floatval($lastS->cout_effectif);
                                                        if (
                                                            floatval($lastS->resultat_atteint) >=
                                                            floatval($firstI->valeur_cible)
                                                        ) {
                                                            $tableauSommeAction[$action->libelle]['achevees'] += 1;
                                                        } elseif (
                                                            floatval($lastS->resultat_atteint) > 0 &&
                                                            floatval($lastS->resultat_atteint) <
                                                                floatval($firstI->valeur_cible)
                                                        ) {
                                                            $tableauSommeAction[$action->libelle]['en_cours'] += 1;
                                                        } else {
                                                            $tableauSommeAction[$action->libelle]['non_demarrees'] += 1;
                                                        }
                                                    }
                                                    // sinon si l'activite est prevue dans la periode mais n'a pas de suivi
                                                    elseif ($activite->periodes->pluck('id')->contains($periode)) {
                                                        $tableauSommeAction[$action->libelle]['nombre'] += 1;
                                                        $tableauSommeAction[$action->libelle]['non_demarrees'] += 1;
                                                    }
                                                @endphp
                                                <td class="center">{{ $firstI->libelle ?? 'N/A' }}</td>
                                                <td class="center">{{ $firstI->unite_mesure ?? 'N/A' }}</td>
                                                <td class="center">{{ $firstI->valeur_cible ?? 'N/A' }}</td>
                                                <td class="center">{{ $lastS->resultat_atteint ?? 'N/E' }}</td>
                                                <td class="center">
                                                    @if (isset($lastS))
                                                        {{ number_format($taux, 2, ',', ' ') }} %
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="center">
                                                    @if (isset($lastS))
                                                        {{ isset($lastS->cout_effectif) ? number_format(floatval($lastS->cout_effectif), 0, ',', ' ') : 'N/A' }}
                                                    @else
                                                        N/E
                                                    @endif
                                                </td>
                                                <td class="center">{{ $lastS->observations ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                            @php
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'nombre'
                                ] += $tableauSommeAction[$action->libelle]['nombre'];
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'achevees'
                                ] += $tableauSommeAction[$action->libelle]['achevees'];
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'en_cours'
                                ] += $tableauSommeAction[$action->libelle]['en_cours'];
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'non_demarrees'
                                ] += $tableauSommeAction[$action->libelle]['non_demarrees'];
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'taux_realisation'
                                ] += $tableauSommeAction[$action->libelle]['taux_realisation'];
                                $tableauSommeProgramme[$programme->numero_programme . ' ' . $programme->libelle][
                                    'cout_effectif'
                                ] += $tableauSommeAction[$action->libelle]['cout_effectif'];
                            @endphp
                        @endforeach
                    @endif
                @endforeach
                <tr style="page-break-after: always">
                    <td colspan="8"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h6 style="text-align: center; margin-top: 40px;">Résumé des réalisations par actions </h6>
    <table>
        <thead>
            <tr style="text-align: center; font-weight: bold; background-color: #E8F5E8;">
                <th>Action</th>
                <th>Nombre d'activités</th>
                <th>Nombre d'activités achevées</th>
                <th>Nombre d'activités en cours</th>
                <th>Nombre d'activités non demarrées</th>
                <th>Taux de réalisation globale</th>
                <th>Coût effectif (en FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tableauSommeAction as $action => $valeur)
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #e8f5f5;">Action
                        {{ $action }}</td>
                    <td class="center">{{ $valeur['nombre'] }}</td>
                    <td class="center">{{ $valeur['achevees'] }}</td>
                    <td class="center">{{ $valeur['en_cours'] }}</td>
                    <td class="center">{{ $valeur['non_demarrees'] }}</td>
                    <td class="center">
                        {{ number_format($valeur['taux_realisation'] / ($valeur['nombre'] == 0 ? 1 : $valeur['nombre']), 2, ',', ' ') }}
                        %</td>
                    <td class="center">{{ number_format($valeur['cout_effectif'], 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h6 style="text-align: center; margin-top: 40px;">Résumé des réalisations par programme </h6>
    <table>
        <thead>
            <tr style="text-align: center; font-weight: bold; background-color: #E8F5E8;">
                <th>Programmes</th>
                <th>Nombre d'activités</th>
                <th>Nombre d'activités achevées</th>
                <th>Nombre d'activités en cours</th>
                <th>Nombre d'activités non demarrées</th>
                <th>Taux de réalisation globale</th>
                <th>Coût effectif (en FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tableauSommeProgramme as $programme => $valeur)
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #e8f5f5;">Programme
                        {{ $programme }}</td>
                    <td class="center">{{ $valeur['nombre'] }}</td>
                    <td class="center">{{ $valeur['achevees'] }}</td>
                    <td class="center">{{ $valeur['en_cours'] }}</td>
                    <td class="center">{{ $valeur['non_demarrees'] }}</td>
                    <td class="center">
                        {{ number_format($valeur['taux_realisation'] / ($valeur['nombre'] == 0 ? 1 : $valeur['nombre']), 2, ',', ' ') }}
                        %</td>
                    <td class="center">{{ number_format($valeur['cout_effectif'], 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script type="text/php">
        if (isset($pdf)) {
            $date = date('d/m/Y H:i:s');
            $text = "{$date} | Page {PAGE_NUM}/{PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 20;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>

</html>
