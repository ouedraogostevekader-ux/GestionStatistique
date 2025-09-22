@extends('layouts.admin')
@section('breadcrumb', 'Rapports d\'activités')
@section('content')
<link rel="stylesheet" href="{{asset('css/rapport.css')}}" type="text/css" />
<section class="content-header">
    <div class="container px-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                  
            </div>
            <div class="col-sm-6">
                <div class="mt-3 d-flex justify-content-end">
                    
                    {{-- Bouton retour --}}
                    <a class="btn btn-secondary" href="{{ route('rapportActivites.index') }}">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    
                </div>
            </div>
        </div>
        <div class="mb-2 d-flex gap-3">
        </div>

    </div>
</section>
<section class="content px-3">
    <div class="card">
        @php
            $periodeModel = App\Models\Periode::find($periode);
            $tableauSommeProgramme = [];
            $tableauSommeAction = [];
        @endphp
        <h3 class="text-center">Rapport d'activités de {{$periodeModel->libelle ?? "Indisponible"}} {{$annee}} généré le {{now()->format('d/m/Y à H:i:s')}} </h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Objectifs/Activités</th>
                    <th style="width: 15%;">Indicateurs de Résultats</th>
                    <th style="width: 8%;">Unité de l'indicateur</th>
                    <th style="width: 8%;">Résultats attendus</th>
                    <th style="width: 8%;">Résultats Atteints</th>
                    <th style="width: 8%;">Taux de réalisation (%)</th>
                    <th style="width: 10%;">Coûts effectifs (en FCFA)</th>
                    <th style="width: 18%;">Observations</th>
                </tr>
            </thead>
            <tbody>
                
                <!-- Autres lignes... -->
                @foreach($data as $programme)
                    <tr>
                        <td colspan="8" class="programme-title">
                            Programme {{$programme->numero_programme }} : {{$programme->libelle}}
                            @php
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle] = ["nombre"=>0, "achevees"=>0, "en_cours"=>0, "non_demarrees"=>0,"taux_realisation"=>0, "cout_effectif"=>0, "activites"=>[]];
                            @endphp
                        </td>
                    </tr>
                    <!-- Objectif stratégique -->
                    @foreach($programme->objectifsStrategiques as $objectifStrategique)
                    
                        <tr>
                            <td rowspan="{{$objectifStrategique->indicateurs->count()>0 ? $objectifStrategique->indicateurs->count() : 1}}" class="objectif-strategique">
                                {{$objectifStrategique->libelle}}
                            </td>
                            @if($objectifStrategique->indicateurs->count() > 0)
                            @php
                                // Obtenir le premier indicateur de l'objectif stratégique
                                $firstIndicateur = $objectifStrategique->indicateurs->first();
                                $lastSuivi = $firstIndicateur ? $firstIndicateur->suivis()->where('periode', $periode)->first() : null;
                                $div = floatval(str_replace(',', '.', $firstIndicateur->valeur_cible ?? 0)) - floatval(str_replace(',', '.', $firstIndicateur->valeur_ref ?? 0));
                                $div = $div != 0 ? $div : 1;
                                $taux = (floatval($lastSuivi->resultat_atteint ?? 0) - floatval(str_replace(',', '.', $firstIndicateur->valeur_ref ?? 0))) *100 / $div;

                            @endphp
                            <td class="center">{{$firstIndicateur->libelle ?? 'N/A'}}</td>
                            <td class="center">{{$firstIndicateur->unite_mesure ?? 'N/A'}}</td>
                            <td class="center">{{$firstIndicateur->valeur_cible ?? 'N/A'}}</td>
                            <td class="center">{{$lastSuivi->resultat_atteint ?? 'N/E'}}</td>
                            <td class="center">@if(isset($lastSuiv) && is_numeric($firstIndicateur->valeur_cible)){{ number_format( $taux , 2, ',', ' ')}} % @else N/A @endif</td>
                            <td class="center">{{$lastSuivi->cout_effectif ?? '-'}}</td>
                            <td class="center">{{$lastSuivi->observations ?? '-'}}</td>
                            @endif
                        </tr>
                        @foreach($objectifStrategique->indicateurs as $indicateur)
                            @if($indicateur->id != $firstIndicateur->id)
                            @php
                                $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                                
                                $div = floatval(str_replace(',', '.', $indicateur->valeur_cible ?? 0)) - floatval(str_replace(',', '.', $indicateur->valeur_ref ?? 0));
                                $div = $div != 0 ? $div : 1;
                                $taux = (floatval($lastSuiv->resultat_atteint ?? 0) - floatval(str_replace(',', '.', $indicateur->valeur_ref ?? 0))) *100 / $div;
                            @endphp
                            <tr>
                                <td class="center">{{$indicateur->libelle}}</td>
                                <td class="center">{{$indicateur->unite_mesure}}</td>
                                <td class="center">{{$indicateur->valeur_cible}}</td>
                                <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                                <td class="center">@if(isset($lastSuiv) && is_numeric($indicateur->valeur_cible)){{number_format($taux, 2, ',', ' ')}} % @else N/A @endif</td>
                                <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <!-- Action -->
                        @foreach($objectifStrategique->actions()->orderBy('libelle')->get() as $action)
                            <tr>
                                <td colspan="8" class="action-title">
                                    {{$action->libelle}}
                                    @php
                                        $tableauSommeAction[$action->libelle] = ["nombre"=>0, "achevees"=>0, "en_cours"=>0, "non_demarrees"=>0,"taux_realisation"=>0, "cout_effectif"=>0];
                                    @endphp
                                </td>
                            </tr>
                            <!-- Objectif spécifique -->
                            @foreach($action->objectifsSpecifiques()->orderBy('libelle')->get() as $objectifsSpecifique)
                            
                                <tr>
                                    <td rowspan="{{$objectifsSpecifique->indicateursProgrammes->count()>0 ? $objectifsSpecifique->indicateursProgrammes->count() : 1}}" class="center middle objectif-strategique">
                                        {{$objectifsSpecifique->libelle}}
                                    </td>
                                    @if($objectifsSpecifique->indicateursProgrammes->count() > 0)
                                    @php
                                        // Obtenir le premier indicateur de l'objectif opérationnel
                                        $firstIndi = $objectifsSpecifique->indicateursProgrammes->first();
                                        $lastSuiv = $firstIndi ? $firstIndi->suivis()->where('periode', $periode)->first() : null;
                                        $div = floatval(str_replace(',', '.', $firstIndi->valeur_cible ?? 0)) - floatval(str_replace(',', '.', $firstIndi->valeur_ref ?? 0));
                                        $div = $div != 0 ? $div : 1;
                                        $taux = (floatval($lastSuiv->resultat_atteint ?? 0) - floatval(str_replace(',', '.', $firstIndi->valeur_ref ?? 0))) *100 / $div;
                                    @endphp
                                    <td class="center">{{$firstIndi->libelle ?? 'N/A'}}</td>
                                    <td class="center">{{$firstIndi->unite_mesure ?? 'N/A' }}</td>
                                    <td class="center">{{$firstIndi->valeur_cible ?? 'N/A'}}</td>
                                    <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                                    <td class="center">@if(isset($lastSuiv) && is_numeric($firstIndi->valeur_cible)){{ number_format($taux , 2, ',', ' ')}} % @else N/A @endif</td>
                                    <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                    <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                                    @endif
                                </tr>
                                @foreach($objectifsSpecifique->indicateursProgrammes as $indicateur)
                                    @if($indicateur->id != $firstIndi->id)
                                    @php
                                        $lastSuiv = $indicateur->suivis()->where('periode', $periode)->first();
                                        $div = floatval(str_replace(',', '.', $indicateur->valeur_cible ?? 0)) - floatval(str_replace(',', '.', $indicateur->valeur_ref ?? 0));
                                        $div = $div != 0 ? $div : 1;
                                        $taux = (floatval($lastSuiv->resultat_atteint ?? 0) - floatval(str_replace(',', '.', $indicateur->valeur_ref ?? 0))) *100 / $div;
                                    @endphp
                                    <tr>
                                        <td class="center">{{$indicateur->libelle}}</td>
                                        <td class="center">{{$indicateur->unite_mesure}}</td>
                                        <td class="center">{{$indicateur->valeur_cible}}</td>
                                        <td class="center">{{$lastSuiv->resultat_atteint ?? 'N/E'}}</td>
                                        <td class="center">@if(isset($lastSuiv) && is_numeric($indicateur->valeur_cible)){{ number_format($taux, 2, ',', ' ')}} % @else N/A @endif</td>
                                        <td class="center">{{$lastSuiv->cout_effectif ?? '-'}}</td>
                                        <td class="center">{{$lastSuiv->observations ?? '-'}}</td>
                                    </tr>
                                    @endif
                                @endforeach
                                <!-- Activité -->
                                @foreach($objectifsSpecifique->activites_sorted as $activite)
                                @if($activite->indicateursProgrammes->count() > 0)
                                    <tr>
                                        <td >
                                            {{$activite->libelle}}
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
                                        
                                            // Si l'activite a un suivi dans la periode
                                            if(isset($lastS)){
                                                $tableauSommeAction[$action->libelle]["nombre"] += 1;
                                                // calcul du taux de realisation = (atteint-reference)/(cible-reference)
                                                $div = floatval(str_replace(',', '.', $firstI->valeur_cible)) - floatval(str_replace(',', '.', $firstI->valeur_ref));
                                                $div = $div != 0 ? $div : 1;
                                                $taux = ((floatval($lastS->resultat_atteint) - floatval(str_replace(',', '.', $firstI->valeur_ref))) / $div) *100 ;
                                                $tableauSommeAction[$action->libelle]["taux_realisation"] += $taux;
                                                
                                                $tableauSommeAction[$action->libelle]["cout_effectif"] += floatval($lastS->cout_effectif); 
                                                if(floatval($lastS->resultat_atteint) >= floatval(str_replace(',', '.', $firstI->valeur_ref))){
                                                    $tableauSommeAction[$action->libelle]["achevees"] += 1;
                                                } elseif(floatval($lastS->resultat_atteint) > 0 && floatval($lastS->resultat_atteint) < floatval(str_replace(',', '.', $firstI->valeur_ref))){
                                                    $tableauSommeAction[$action->libelle]["en_cours"] += 1;
                                                } else {
                                                    $tableauSommeAction[$action->libelle]["non_demarrees"] += 1;
                                                }
                                            } // sinon si l'activite est prevue dans la periode mais n'a pas de suivi
                                            elseif($activite->periodes->pluck('id')->contains($periode)){
                                                $tableauSommeAction[$action->libelle]["nombre"] += 1;
                                                $tableauSommeAction[$action->libelle]["non_demarrees"] += 1;
                                            }
                                        @endphp
                                        <td class="center">{{$firstI->libelle ?? 'N/A'}}</td>
                                        <td class="center">{{$firstI->unite_mesure ?? 'N/A'}}</td>
                                        <td class="center">{{$firstI->valeur_cible ?? 'N/A'}}</td>
                                        <td class="center">{{$lastS->resultat_atteint ?? 'N/E'}}</td>
                                        <td class="center">@if(isset($lastS)){{ number_format($taux , 2, ',', ' ')}} @else N/A @endif</td>
                                        <td class="center">@if(isset($lastS)){{isset($lastS->cout_effectif) ? number_format(floatval($lastS->cout_effectif), 0, ',', ' ') : 'N/A'}} @else N/E @endif</td>
                                        <td class="center">{{$lastS->observations ?? '-'}}</td>                                    
                                    </tr>
                                @endif 
                                @endforeach
                            
                            @endforeach 
                            @php
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["nombre"] += $tableauSommeAction[$action->libelle]["nombre"];
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["achevees"] += $tableauSommeAction[$action->libelle]["achevees"];
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["en_cours"] += $tableauSommeAction[$action->libelle]["en_cours"];
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["non_demarrees"] += $tableauSommeAction[$action->libelle]["non_demarrees"];
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["taux_realisation"] += $tableauSommeAction[$action->libelle]["taux_realisation"];
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["cout_effectif"] += $tableauSommeAction[$action->libelle]["cout_effectif"];
                                // Ajouter l'action à la liste des activités du programme
                                $tableauSommeProgramme[$programme->numero_programme. " ". $programme->libelle]["activites"][$action->libelle] = $tableauSommeAction[$action->libelle];
                            @endphp
                        @endforeach
                    
                    @endforeach
                    <tr style="page-break-after: always">
                        <td colspan="8"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h1 style=" page-break-before: always; text-align: center"> Synthèse des activités</h1>
        <h2 style="text-align: center; margin-top: 40px;">Résumé des réalisations par actions </h2>
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
                        <td style=" font-weight: bold; background-color: #e8f5f5;">{{$action}}</td>
                        <td class="center">{{$valeur["nombre"]}}</td>
                        <td class="center">{{$valeur["achevees"]}}</td>
                        <td class="center">{{$valeur["en_cours"]}}</td>
                        <td class="center">{{$valeur["non_demarrees"]}}</td>
                        <td class="center">{{ number_format($valeur["taux_realisation"]/($valeur["nombre"] == 0 ? 1 : $valeur["nombre"]), 2, ',', ' ') }}</td>
                        <td class="center">{{number_format($valeur["cout_effectif"], 0, ',', ' ')}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h2 style="text-align: center; margin-top: 40px; page-break-before: always">Résumé des réalisations par programme </h2>
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
                        <td style="font-weight: bold; background-color: #e8f5f5;">{{$programme}}</td>
                        <td class="center">{{$valeur["nombre"]}}</td>
                        <td class="center">{{$valeur["achevees"]}}</td>
                        <td class="center">{{$valeur["en_cours"]}}</td>
                        <td class="center">{{$valeur["non_demarrees"]}}</td>
                        <td class="center">{{ number_format($valeur["taux_realisation"]/($valeur["nombre"] == 0 ? 1 : $valeur["nombre"]), 2, ',', ' ') }}</td>
                        <td class="center">{{number_format($valeur["cout_effectif"], 0, ',', ' ')}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h2 style="text-align: center; margin-top: 40px; page-break-before: always">Résumé des réalisations par programme et action </h2>
        <table>
            <thead>
                <tr style="text-align: center; font-weight: bold; background-color: #E8F5E8;">
                    <th>Programmes/Actions</th>
                    <th>Nombre d'activités</th>
                    <th>Nombre d'activités achevées</th>
                    <th>Nombre d'activités en cours</th>
                    <th>Nombre d'activités non demarrées</th>
                    <th>Taux de réalisation globale</th>
                    <th>Coût effectif (en FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $nombreTotal = 0;
                    $acheveesTotal = 0;
                    $enCoursTotal = 0;
                    $nonDemarreesTotal = 0;
                    $tauxRealisationTotal = 0;
                    $coutEffectifTotal = 0;
                @endphp
                @foreach($tableauSommeProgramme as $programme => $valeur)
                    @php
                        $nombreTotal += $valeur["nombre"];
                        $acheveesTotal += $valeur["achevees"];
                        $enCoursTotal += $valeur["en_cours"];
                        $nonDemarreesTotal += $valeur["non_demarrees"];
                        $tauxRealisationTotal += $valeur["taux_realisation"];
                        $coutEffectifTotal += $valeur["cout_effectif"];
                    @endphp
                    <tr style="font-weight: bold; background-color: #e8f5f5;">
                        <td >{{$programme}}</td>
                        <td class="center">{{$valeur["nombre"]}}</td>
                        <td class="center">{{$valeur["achevees"]}}</td>
                        <td class="center">{{$valeur["en_cours"]}}</td>
                        <td class="center">{{$valeur["non_demarrees"]}}</td>
                        <td class="center">{{ number_format($valeur["taux_realisation"]/($valeur["nombre"] == 0 ? 1 : $valeur["nombre"]), 2, ',', ' ') }}</td>
                        <td class="center">{{number_format($valeur["cout_effectif"], 0, ',', ' ')}}</td>
                    </tr>
                    @foreach($valeur["activites"] as $action => $val)
                        <tr>
                            <td>{{$action}}</td>
                            <td class="center">{{$val["nombre"]}}</td>
                            <td class="center">{{$val["achevees"]}}</td>
                            <td class="center">{{$val["en_cours"]}}</td>
                            <td class="center">{{$val["non_demarrees"]}}</td>
                            <td class="center">{{ number_format($val["taux_realisation"]/($val["nombre"] == 0 ? 1 : $val["nombre"]), 2, ',', ' ') }}</td>
                            <td class="center">{{number_format($val["cout_effectif"], 0, ',', ' ')}}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr style="text-align: center; font-weight: bold; background-color: #eccc86;">
                    <td style="text-align: center;">Totaux</td>
                    <td class="center">{{$nombreTotal}}</td>
                    <td class="center">{{$acheveesTotal}}</td>
                    <td class="center">{{$enCoursTotal}}</td>
                    <td class="center">{{$nonDemarreesTotal}}</td>
                    <td class="center">{{ number_format($tauxRealisationTotal/($nombreTotal == 0 ? 1 : $nombreTotal), 2, ',', ' ') }}</td>
                    <td class="center">{{number_format($coutEffectifTotal, 0, ',', ' ')}}</td>
                </tr>
            </tbody>
        </table>

    </div>
</section>
@endsection