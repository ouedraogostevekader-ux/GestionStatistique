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
            {{-- Filtre selon le programme d'activites --}}
            {{-- <div>
                <label for="filterProgramme" class="form-label">Filtrer par programme d'activités :</label>
                <select id="filterProgramme" class="form-select">
                    <option value="">-- Tous --</option>
                    @foreach ($programmeActivites as $programme)
                        <option value="{{ $programme->libelle }}">{{ $programme->libelle }}</option>
                    @endforeach
                </select>
            </div> --}}

            {{-- Filtre selon la periode --}}
            {{-- <div>
                <label for="filterPeriode" class="form-label">Filtrer par periode :</label>
                <select id="filterPeriode" class="form-select">
                    <option value="">-- Toutes --</option>
                    @foreach ($periodes as $key=>$periode)
                        <option value="{{ $key }}">{{ $periode }}</option>
                    @endforeach
                </select>
            </div> --}}

        </div>

    </div>
</section>
<section class="content px-3">
    <div class="card">
        <h3 class="text-center">Rapport d'activités de {{$periode}} {{$annee}} généré le {{now()->format('d/m/Y à H:i:s')}} </h3>
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
                            {{$programme->libelle}}
                        </td>
                    </tr>
                    <!-- Objectif stratégique -->
                    @foreach($programme->objectifsStrategiques as $objectifStrategique)
                    @if($objectifStrategique->indicateurs->count() > 0)
                        <tr>
                            <td rowspan="{{$objectifStrategique->indicateurs->count()}}" class="objectif-strategique">
                                {{$objectifStrategique->libelle}}
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
                                    {{$action->libelle}}
                                </td>
                            </tr>
                            <!-- Objectif specifique -->
                            @foreach($action->objectifsSpecifiques as $objectifsSpecifique)
                            @if($objectifsSpecifique->indicateursProgrammes->count() > 0)
                                <tr>
                                    <td rowspan="{{$objectifsSpecifique->indicateursProgrammes->count()}}" class="center middle">
                                        {{$objectifsSpecifique->libelle}}
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
    </div>
</section>
@endsection