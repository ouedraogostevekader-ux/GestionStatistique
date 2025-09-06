<!DOCTYPE html>
<html>
<head>
    @php
        $periodeModel = App\Models\Periode::find($periode);
    @endphp
    <meta charset="utf-8">
    <title>Rapport d'activités de {{$periodeModel->libelle ?? "Indisponible" }} {{$annee}}</title>
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
        th, td {
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
    <h3>Rapport d'activités de {{$periodeModel->libelle ?? "Indisponible" }} {{$periode}} {{$annee}} {{$date->format('d-m-Y')}} généré le {{now()->format('d/m/Y à H:i:s')}} </h3>
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
            @foreach($data as $programme)
                <tr>
                    <td colspan="8" class="programme-title">
                        Programme {{$programme->numero_programme }} : {{$programme->libelle}}
                    </td>
                </tr>
                <!-- Objectif stratégique -->
                @foreach($programme->objectifsStrategiques as $objectifStrategique)
                @if($objectifStrategique->indicateurs->count() > 0)
                    <tr>
                        <td rowspan="{{$objectifStrategique->indicateurs->count()}}" class="objectif-strategique">
                            Objectif stratégique : {{$objectifStrategique->libelle}}
                        </td>
                        @php
                            // Obtenir le premier indicateur de l'objectif stratégique
                            $firstIndicateur = $objectifStrategique->indicateurs->first();
                            $lastSuivi = $firstIndicateur ? $firstIndicateur->suivis()->where('periode', $periode)->first() : null;
                        @endphp
                        <td class="center">{{$firstIndicateur->libelle ?? 'N/A'}}</td>
                        <td class="center">{{$firstIndicateur->unite_mesure ?? 'N/A'}}</td>
                        <td class="center">{{$firstIndicateur->valeur_cible ?? 'N/A'}}</td>
                        <td class="center">{{$lastSuivi->resultat_atteint ?? 'N/E'}}</td>
                        <td class="center">{{ number_format( ($lastSuivi->resultat_atteint ?? 0) *100 / ($firstIndicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                        <td class="center">{{$lastSuivi->cout_effectif ?? '-'}}</td>
                        <td class="center">{{$lastSuivi->observations ?? '-'}}</td>
                    </tr>
                    @foreach($objectifStrategique->indicateurs as $indicateur)
                        @if($indicateur->id != $firstIndicateur->id)
                        @php
                            $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                        @endphp
                        <tr>
                            <td class="center">{{$indicateur->libelle}}</td>
                            <td class="center">{{$indicateur->unite_mesure}}</td>
                            <td class="center">{{$indicateur->valeur_cible}}</td>
                            <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                            <td class="center">{{number_format( ($lastSuiv->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                            <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                            <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                        </tr>
                        @endif
                    @endforeach
                    <!-- Action -->
                    @foreach($objectifStrategique->actions as $action)
                        <tr>
                            <td colspan="8" class="action-title">
                                Action : {{$action->libelle}}
                            </td>
                        </tr>
                        <!-- Objectif spécifique -->
                        @foreach($action->objectifsSpecifiques as $objectifsSpecifique)
                        @if($objectifsSpecifique->indicateursProgrammes->count() > 0)
                            <tr>
                                <td rowspan="{{$objectifsSpecifique->indicateursProgrammes->count()}}" class="center middle objectif-strategique">
                                    Objectif spécifique : {{$objectifsSpecifique->libelle}}
                                </td>
                                @php
                                    // Obtenir le premier indicateur de l'objectif opérationnel
                                    $firstIndi = $objectifsSpecifique->indicateursProgrammes->first();
                                    $lastSuiv = $firstIndi ? $firstIndi->suivis()->where('periode', $periode)->first() : null;
                                @endphp
                                <td class="center">{{$firstIndi->libelle ?? 'N/A'}}</td>
                                <td class="center">{{$firstIndi->unite_mesure ?? 'N/A' }}</td>
                                <td class="center">{{$firstIndi->valeur_cible ?? 'N/A'}}</td>
                                <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                                <td class="center">{{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($firstIndi->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                                <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                            </tr>
                            @foreach($objectifsSpecifique->indicateursProgrammes as $indicateur)
                                @if($indicateur->id != $firstIndi->id)
                                @php
                                    $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                                @endphp
                                <tr>
                                    <td class="center">{{$indicateur->libelle}}</td>
                                    <td class="center">{{$indicateur->unite_mesure}}</td>
                                    <td class="center">{{$indicateur->valeur_cible}}</td>
                                    <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                                    <td class="center">{{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                                    <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                    <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                                </tr>
                                @endif
                            @endforeach
                            <!-- Activité -->
                            @foreach($objectifsSpecifique->activites as $activite)
                            @if($activite->indicateursProgrammes->count() > 0)
                                <tr>
                                    <td >
                                        Activité : {{$activite->libelle}}
                                    </td>
                                    @php
                                        // Obtenir le premier indicateur de l'activité
                                        $firstI = $activite->indicateursProgrammes->first();
                                        $lastS = $firstI ? $firstI->suivis()->where('periode', $periode)->first() : null;
                                    @endphp
                                    <td class="center">{{$firstI->libelle ?? 'N/A'}}</td>
                                    <td class="center">{{$firstI->unite_mesure ?? 'N/A'}}</td>
                                    <td class="center">{{$firstI->valeur_cible ?? 'N/A'}}</td>
                                    <td class="center">{{$lastS->resultat_atteint ?? 'N/E'}}</td>
                                    <td class="center">{{ number_format(($lastS->resultat_atteint ?? 0) *100 / ($firstI->valeur_cible ?? 1) , 2, ',', ' ')}} %</td>
                                    <td class="center">{{isset($lastS) & isset($lastS->cout_effectif) ? number_format($lastS->cout_effectif, 2, ',', ' ') : 'N/E'}}</td>
                                    <td class="center">{{$lastS->observations ?? '-'}}</td>                                    
                                </tr>
                            @endif 
                            @endforeach
                        @endif
                        @endforeach 
                    @endforeach
                @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>