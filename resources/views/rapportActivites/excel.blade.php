
@php
    $periodeModel = App\Models\Periode::find($periode);
@endphp
<h3>Rapport d'activités de {{$periodeModel->libelle ?? "Indisponible"}} {{$annee}} {{$date->format('d-m-Y')}} généré le {{now()->format('d/m/Y à H:i')}}</h3>

<table>
    <thead>
        <tr>
            <th>Objectifs/Activités</th>
            <th>Indicateurs de Résultats</th>
            <th>Unité de l'indicateur</th>
            <th>Résultats attendus</th>
            <th>Résultats Atteints</th>
            <th>Taux de réalisation (%)</th>
            <th>Coûts effectifs (en milliers de FCFA)</th>
            <th>Observations</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $programme)
            <tr>
                <td colspan="8" style="text-align: center; font-weight: bold; background-color: #E8F5E8;">
                    Programme {{$programme->numero_programme }} : {{$programme->libelle}}
                </td>
            </tr>
            <!-- Objectif stratégique -->
            @foreach($programme->objectifsStrategiques as $objectifStrategique)
            @if($objectifStrategique->indicateurs->count() > 0)
                <tr>
                    <td rowspan="{{$objectifStrategique->indicateurs->count()}}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                        {{$objectifStrategique->libelle}}
                    </td>
                    @php
                        // Obtenir le premier indicateur de l'objectif stratégique
                        $firstIndicateur = $objectifStrategique->indicateurs->count()>0 ? $objectifStrategique->indicateurs->first() : null;
                        $lastSuivi = $firstIndicateur ? $firstIndicateur->suivis()->where('periode', $periode)->first() : null;
                    @endphp
                    <td>{{$firstIndicateur->libelle ?? 'N/A'}}</td>
                    <td>{{$firstIndicateur->unite_mesure ?? 'N/A'}}</td>
                    <td>{{$firstIndicateur->valeur_cible ?? 'N/A'}}</td>
                    <td>{{$lastSuivi->resultat_atteint ?? 'N/A'}}</td>
                    <td >{{ number_format( ($lastSuivi->resultat_atteint ?? 0) *100 / ($firstIndicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                    
                    <td >{{$lastSuivi->cout_effectif ?? '-'}}</td>
                    <td>{{$lastSuivi->observations ?? 'N/A'}}</td>
                </tr>
                @foreach($objectifStrategique->indicateurs as $indicateur)
                    @if($indicateur->id != $firstIndicateur->id)
                    @php
                        $lastSuivi = $indicateur->suivis()->where('periode', $periode)->first();
                    @endphp
                    <tr>
                        <td>{{$indicateur->libelle}}</td>
                        <td>{{$indicateur->unite_mesure}}</td>
                        <td>{{$indicateur->valeur_cible}}</td>
                        <td>{{$lastSuivi->resultat_atteint ?? 'N/A'}}</td>
                        <td >{{number_format( ($lastSuivi->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                        <td >{{$lastSuivi->cout_effectif ?? '-'}}</td>
                        <td>{{$lastSuivi->observations ?? 'N/A'}}</td>
                    </tr>
                    @endif
                @endforeach
                <!-- objectif strategique -->
                @foreach($objectifStrategique->actions as $action)
                    <tr>
                        <td colspan="8" style="text-align: center; font-weight: bold; background-color: #e8f5f5;">
                            {{$action->libelle}}
                        </td>
                    </tr>
                    <!-- Objectif Opérationnel -->
                    @foreach($action->objectifsSpecifiques as $objectifsSpecifique)
                    @if($objectifsSpecifique->indicateursProgrammes->count() > 0)
                        <tr>
                            <td rowspan="{{$objectifsSpecifique->indicateursProgrammes->count()}}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{$objectifsSpecifique->libelle}}
                            </td>
                            @php
                                // Obtenir le premier indicateur de l'objectif opérationnel
                                $firstIndi = $objectifsSpecifique->indicateursProgrammes->count()>0 ? $objectifsSpecifique->indicateursProgrammes->first() : null;
                                $lastSuiv = $firstIndi ? $firstIndi->suivis()->where('periode', $periode)->first() : null;
                            @endphp
                            <td>{{$firstIndi->libelle ?? 'N/A'}}</td>
                            <td>{{$firstIndi->unite_mesure ?? 'N/A' }}</td>
                            <td>{{$firstIndi->valeur_cible ?? 'N/A'}}</td>
                            <td>{{$lastSuiv->resultat_atteint ?? 'N/A'}}</td>
                            <td >{{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($firstIndi->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                            <td >{{$lastSuiv->cout_effectif ?? '-'}}</td>
                            <td>{{$lastSuiv->observations ?? 'N/A'}}</td>
                        </tr>
                        @foreach($objectifsSpecifique->indicateursProgrammes as $indicateur)
                            @if($indicateur->id != $firstIndi->id)
                            @php
                                $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                            @endphp
                            <tr>
                                <td>{{$indicateur->libelle}}</td>
                                <td>{{$indicateur->unite_mesure}}</td>
                                <td>{{$indicateur->valeur_cible}}</td>
                                <td>{{$lastSuiv->resultat_atteint ?? 'N/A'}}</td>
                                <td>{{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} %</td>
                                <td>{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                <td>{{$lastSuiv->observations ?? 'N/A'}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <!-- Activité -->
                        @foreach($objectifsSpecifique->activites as $activite)
                        @if($activite->indicateursProgrammes->count()>0)
                            <tr>
                                <td  style="text-align: center;">
                                    {{$activite->libelle}}
                                </td>
                                @php
                                    // Obtenir le premier indicateur de l'activité
                                    $firstI = $activite->indicateursProgrammes->first();
                                    $lastS = $firstI ? $firstI->suivis()->where('periode', $periode)->first() : null;
                                @endphp
                                <td class="center">{{$firstI->libelle ?? 'N/A'}}</td>
                                <td class="center">{{$firstI->unite_mesure ?? 'N/A'}}</td>
                                <td class="center">{{$firstI->valeur_cible ?? 'N/A'}}</td>
                                <td class="center">{{$lastS->resultat_atteint ?? 'N/A'}}</td>
                                <td class="center">{{ number_format(($lastS->resultat_atteint ?? 0) *100 / ($firstI->valeur_cible ?? 1) , 2, ',', ' ')}} %</td>
                                <td class="center">{{isset($lastS) & isset($lastS->cout_effectif) ? number_format($lastS->cout_effectif, 2, ',', ' ') : 'N/E'}}</td>
                                <td class="center">{{$lastS->observations ?? 'N/A'}}</td>
                               
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