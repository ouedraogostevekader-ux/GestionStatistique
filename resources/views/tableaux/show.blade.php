@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $tableau->libelle }}</h2>
            <p class="text-muted">{{ $tableau->description }}</p>
            <small class="text-info">Catégorie: {{ $tableau->categorie->libelle }}</small>
        </div>
        <div>
            <a href="{{ route('tableaux.structure', $tableau->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-cogs"></i> Structure tableau
            </a>
            <a href="{{ route('tableaux.saisie', $tableau->id) }}" class="btn btn-success">
                <i class="fas fa-edit"></i> Saisir Données
            </a>
            <a href="{{ route('tableaux.affecter', $tableau->id) }}" class="btn btn-success">
                <i class="fas fa-edit"></i> Affecter tableau
            </a>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($lignes->count() > 0 && $colonnes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle">Libellé</th>
                                @foreach($colonnes as $colonne)
                                    @if($colonne->enfants->count() > 0)
                                        <th colspan="{{ $colonne->enfants->count() }}" class="text-center">
                                            {{ $colonne->colonne_ligne }}
                                        </th>
                                    @else
                                        <th rowspan="2" class="align-middle text-center">
                                            {{ $colonne->colonne_ligne }}
                                        </th>
                                    @endif
                                @endforeach
                                <th rowspan="2" class="align-middle text-center">Total</th>
                            </tr>
                            <tr>
                                @foreach($colonnes as $colonne)
                                    @foreach($colonne->enfants as $sousColonne)
                                        <th class="text-center">{{ $sousColonne->colonne_ligne }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lignes as $ligne)
                                <tr>
                                    <td class="fw-bold">{{ $ligne->nom_ligne }}</td>
                                    @foreach($colonnes as $colonne)
                                        @if($colonne->enfants->count() > 0)
                                            @foreach($colonne->enfants as $sousColonne)
                                                <td class="text-center">
                                                    @php
                                                        $celluleKey = $ligne->id . '_' . $sousColonne->id . '_structure_1';
                                                        $valeur = $soumissions->get($celluleKey)->valeur ?? '-';
                                                    @endphp
                                                    {{ $valeur }}
                                                </td>
                                            @endforeach
                                        @else
                                            <td class="text-center">
                                                @php
                                                    $celluleKey = $ligne->id . '_' . $colonne->id . '_structure_1';
                                                    $valeur = $soumissions->get($celluleKey)->valeur ?? '-';
                                                @endphp
                                                {{ $valeur }}
                                            </td>
                                        @endif
                                    @endforeach
                                    <td class="text-center fw-bold">
                                        {{-- Calcul du total pour cette ligne --}}
                                        0.00
                                    </td>
                                </tr>
                                
                                {{-- Afficher les sous-lignes --}}
                                @foreach($ligne->enfants as $sousLigne)
                                    <tr class="table-secondary">
                                        <td class="ps-4">{{ $sousLigne->nom_ligne }}</td>
                                        @foreach($colonnes as $colonne)
                                            @if($colonne->enfants->count() > 0)
                                                @foreach($colonne->enfants as $sousColonne)
                                                    <td class="text-center">
                                                        @php
                                                            $celluleKey = $sousLigne->id . '_' . $sousColonne->id . '_structure_1';
                                                            $valeur = $soumissions->get($celluleKey)->valeur ?? '-';
                                                        @endphp
                                                        {{ $valeur }}
                                                    </td>
                                                @endforeach
                                            @else
                                                <td class="text-center">
                                                    @php
                                                        $celluleKey = $sousLigne->id . '_' . $colonne->id . '_structure_1';
                                                        $valeur = $soumissions->get($celluleKey)->valeur ?? '-';
                                                    @endphp
                                                    {{ $valeur }}
                                                </td>
                                            @endif
                                        @endforeach
                                        <td class="text-center">0.00</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            
                            {{-- Ligne Ensemble (Total) --}}
                            <tr class="table-primary fw-bold">
                                <td>Ensemble</td>
                                @foreach($colonnes as $colonne)
                                    @if($colonne->enfants->count() > 0)
                                        @foreach($colonne->enfants as $sousColonne)
                                            <td class="text-center">0.00</td>
                                        @endforeach
                                    @else
                                        <td class="text-center">0.00</td>
                                    @endif
                                @endforeach
                                <td class="text-center">0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <h5>Tableau vide</h5>
                    <p>Ce tableau n'a pas encore de structure définie.</p>
                    <a href="{{ route('tableaux.structure', $tableau->id) }}" class="btn btn-primary">
                        Définir la structure
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection