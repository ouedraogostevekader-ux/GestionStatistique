
@php
    $periodeModel = App\Models\Periode::find($periode);
    $tableauSommeProgramme = [];
    $tableauSommeAction = [];
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
                    @php
                        $tableauSommeProgramme[$$programme->numero_programme. " ". $programme->libelle] = ["nombre"=>0, "achevees"=>0, "en_cours"=>0, "non_demarrees"=>0,"taux_realisation"=>0, "cout_effectif"=>0];
                    @endphp
                </td>
            </tr>
            <!-- Objectif stratégique -->
            @foreach($programme->objectifsStrategiques as $objectifStrategique)
            @if($objectifStrategique->indicateurs->count() > 0)
                <tr>
                    <td rowspan="{{$objectifStrategique->indicateurs->count()}}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                        Objectif stratégique : {{$objectifStrategique->libelle}}
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
                    <td >@if(isset($lastSuivi) && is_numeric($firstIndicateur->valeur_cible)){{ number_format( ($lastSuivi->resultat_atteint ?? 0) *100 / ($firstIndicateur->valeur_cible ?? 1), 2, ',', ' ')}} % @else N/A @endif</td>
                    
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
                        <td >@if(isset($lastSuivi) && is_numeric($indicateur->valeur_cible)){{number_format( ($lastSuivi->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} % @else N/A @endif </td>
                        <td >{{$lastSuivi->cout_effectif ?? '-'}}</td>
                        <td>{{$lastSuivi->observations ?? 'N/A'}}</td>
                    </tr>
                    @endif
                @endforeach
                <!-- Actions -->
                @foreach($objectifStrategique->actions()->orderBy('libelle')->get() as $action)
                    <tr>
                        <td colspan="8" style="text-align: center; font-weight: bold; background-color: #e8f5f5;">
                            Action : {{$action->libelle}}
                            @php
                                $tableauSommeAction[$action->libelle] = ["nombre"=>0, "achevees"=>0, "en_cours"=>0, "non_demarrees"=>0,"taux_realisation"=>0, "cout_effectif"=>0];
                            @endphp
                        </td>
                    </tr>
                    <!-- Objectif Specifique -->
                    @foreach($action->objectifsSpecifiques()->orderBy('libelle')->get() as $objectifsSpecifique)
                    @if($objectifsSpecifique->indicateursProgrammes->count() > 0)
                        <tr>
                            <td rowspan="{{$objectifsSpecifique->indicateursProgrammes->count()}}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                Objectif spécifique {{$objectifsSpecifique->libelle}}
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
                            <td>@if(isset($lastSuiv) && is_numeric($firstIndi->valeur_cible)){{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($firstIndi->valeur_cible ?? 1), 2, ',', ' ')}} % @else N/A @endif</td>
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
                                <td>@if(isset($lastSuiv) && is_numeric($indicateur->valeur_cible)){{ number_format(($lastSuiv->resultat_atteint ?? 0) *100 / ($indicateur->valeur_cible ?? 1), 2, ',', ' ')}} % @else N/A @endif</td>
                                <td>{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                <td>{{$lastSuiv->observations ?? 'N/A'}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <!-- Activité -->
                        @foreach($objectifsSpecifique->activites()->orderBy('libelle')->get() as $activite)
                        @if($activite->indicateursProgrammes->count()>0)
                            <tr>
                                <td  style="text-align: center;">
                                    Activité : {{$activite->libelle}}
                                </td>
                                @php
                                    // Obtenir le premier indicateur de l'activité
                                    $firstI = $activite->indicateursProgrammes->first();
                                    $lastS = $firstI ? $firstI->suivis()->where('periode', $periode)->first() : null;
                                    if($periodeModel->libelle == "T4"){
                                        // recuperer le dernier suivis ordonné par le libellé de la relation période du suivi
                                        $lastS = $firstI ? $firstI->suivis()
                                        ->join('periodes', 'suivis.periode', '=', 'periodes.id')
                                        ->orderBy('periodes.libelle', 'desc')->first() : null;
                                    }
                                    if(isset($lastS)){
                                        $tableauSommeAction[$action->libelle]["nombre"] += 1;
                                        if(is_numeric($lastS->resultat_atteint) && is_numeric($firstI->valeur_cible)){
                                            $tableauSommeAction[$action->libelle]["taux_realisation"] += ($lastS->resultat_atteint *100 / ($firstI->valeur_cible ?? 1));
                                        }
                                        $tableauSommeAction[$action->libelle]["cout_effectif"] += isset($lastS->cout_effectif) ? $lastS->cout_effectif : 0;
                                        if($lastS->resultat_atteint >= ($firstI->valeur_cible ?? 0)){
                                            $tableauSommeAction[$action->libelle]["achevees"] += 1;
                                        } elseif($lastS->resultat_atteint > 0 && $lastS->resultat_atteint < ($firstI->valeur_cible ?? 0)){
                                            $tableauSommeAction[$action->libelle]["en_cours"] += 1;
                                        } else {
                                            $tableauSommeAction[$action->libelle]["non_demarrees"] += 1;
                                        }
                                    }
                                @endphp
                                <td class="center">{{$firstI->libelle ?? 'N/A'}}</td>
                                <td class="center">{{$firstI->unite_mesure ?? 'N/A'}}</td>
                                <td class="center">{{$firstI->valeur_cible ?? 'N/A'}}</td>
                                <td class="center">{{$lastS->resultat_atteint ?? 'N/A'}}</td>
                                <td class="center">@if(is_numeric($firstI->valeur_cible)){{ number_format(($lastS->resultat_atteint ?? 0) *100 / ($firstI->valeur_cible ?? 1) , 2, ',', ' ')}} % @else N/A @endif</td>
                                <td class="center">@if(isset($lastS) && is_numeric($lastS->resultat_atteint)){{isset($lastS->cout_effectif) ? number_format($lastS->cout_effectif, 2, ',', ' ') : 'N/E'}} @else N/E @endif</td>
                                <td class="center">{{$lastS->observations ?? 'N/A'}}</td>
                               
                            </tr>
                        @endif
                        @endforeach
                    @endif
                    @endforeach 
                    @php
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["nombre"] += $tableauSommeAction[$action->libelle]["nombre"];
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["achevees"] += $tableauSommeAction[$action->libelle]["achevees"];
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["en_cours"] += $tableauSommeAction[$action->libelle]["en_cours"];
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["non_demarrees"] += $tableauSommeAction[$action->libelle]["non_demarrees"];
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["taux_realisation"] += $tableauSommeAction[$action->libelle]["taux_realisation"];
                        $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["cout_effectif"] += $tableauSommeAction[$action->libelle]["cout_effectif"];
                    @endphp
                @endforeach
            @endif    
            @endforeach
        @endforeach

    </tbody>
</table>



<h6 style="text-align: center;">Résumé des réalisations par actions </h6>
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
        @foreach($tableauSommeAction as $action => $valeur)
            <tr>
                <td style="text-align: center; font-weight: bold; background-color: #e8f5f5;">Action : {{$action}}</td>
                <td class="center">{{$valeur["nombre"]}}</td>
                <td class="center">{{$valeur["achevees"]}}</td>
                <td class="center">{{$valeur["en_cours"]}}</td>
                <td class="center">{{$valeur["non_demarrees"]}}</td>
                <td class="center">{{ number_format($valeur["taux_realisation"]/($valeur["nombre"] == 0 ? 1 : $valeur["nombre"]) * 100, 2, ',', ' ') }} %</td>
                <td class="center">{{$valeur["cout_effectif"]}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h6 style="text-align: center;">Résumé des réalisations par programme </h6>
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
        @foreach($tableauSommeProgramme as $programme => $valeur)
            <tr>
                <td style="text-align: center; font-weight: bold; background-color: #e8f5f5;">Programme : {{$programme}}</td>
                <td class="center">{{$valeur["nombre"]}}</td>
                <td class="center">{{$valeur["achevees"]}}</td>
                <td class="center">{{$valeur["en_cours"]}}</td>
                <td class="center">{{$valeur["non_demarrees"]}}</td>
                <td class="center">{{ number_format($valeur["taux_realisation"]/($valeur["nombre"] == 0 ? 1 : $valeur["nombre"]) * 100, 2, ',', ' ') }} %</td>
                <td class="center">{{$valeur["cout_effectif"]}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
