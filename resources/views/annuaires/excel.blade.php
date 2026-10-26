<table>
    <thead>
        <tr>
            <th colspan="5" style="font-size: 14pt; font-weight: bold; text-align: center;">
                ANNUAIRE STATISTIQUE - ÉDITION {{ $version->annee ?? '' }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="font-size: 10pt; color: #555555; text-align: center;">
                Ministère des Infrastructures - Direction Générale des Études et des Statistiques Sectorielles (DGESS)
            </th>
        </tr>
        <tr><th></th></tr>
    </thead>
</table>

@foreach ($tableaux as $tableau)
    @php
        $isTotalOrEnsemble = fn($nom) => in_array(mb_strtolower(trim($nom)), ['ensemble', 'total', 'total général', 'totaux']);
        $lignesRacines = $tableau->lignesTableau->whereNull('parent_id')
            ->filter(fn($l) => $l->ordre != 1 && !$isTotalOrEnsemble($l->nom_ligne));
        $colonnesRacines = $tableau->colonnesTableau->whereNull('parent_id');
        $headerLigne = $tableau->lignesTableau->firstWhere('ordre', 1);
        $headerTitre = $headerLigne ? $headerLigne->nom_ligne : 'Libellé';
        $hasSousColonnes = $colonnesRacines->contains(fn($c) => $c->enfants && $c->enfants->count() > 0);
    @endphp

    <table>
        <thead>
            @if ($tableau->categorie)
                <tr>
                    <th colspan="4" style="font-size: 9pt; font-weight: bold; color: #4b5563;">
                        Catégorie : {{ $tableau->categorie->libelle }}
                    </th>
                </tr>
            @endif
            <tr>
                <th colspan="4" style="font-size: 11pt; font-weight: bold; background-color: #f1f5f9; color: #003366;">
                    {{ $tableau->libelle }}
                </th>
            </tr>
            <tr>
                <th @if($hasSousColonnes) rowspan="2" @endif style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #94a3b8; text-align: left;">
                    {{ $headerTitre }}
                </th>
                @foreach ($colonnesRacines as $colonne)
                    @if ($colonne->enfants && $colonne->enfants->count() > 0)
                        <th colspan="{{ $colonne->enfants->count() }}" style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #94a3b8; text-align: center;">
                            {{ $colonne->colonne_ligne }}
                        </th>
                    @else
                        <th @if($hasSousColonnes) rowspan="2" @endif style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #94a3b8; text-align: center;">
                            {{ $colonne->colonne_ligne }}
                        </th>
                    @endif
                @endforeach
                <th @if($hasSousColonnes) rowspan="2" @endif style="background-color: #cbd5e1; font-weight: bold; border: 1px solid #94a3b8; text-align: center;">
                    Total
                </th>
            </tr>
            @if ($hasSousColonnes)
                <tr>
                    @foreach ($colonnesRacines as $colonne)
                        @if ($colonne->enfants && $colonne->enfants->count() > 0)
                            @foreach ($colonne->enfants as $sousColonne)
                                <th style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #94a3b8; text-align: center;">
                                    {{ $sousColonne->colonne_ligne }}
                                </th>
                            @endforeach
                        @endif
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($lignesRacines as $ligne)
                @php
                    $hasSousLignes = $ligne->enfants && $ligne->enfants->count() > 0;
                @endphp

                @if ($hasSousLignes)
                    {{-- Ligne Parente --}}
                    <tr>
                        <td style="font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                            {{ $ligne->nom_ligne }}
                        </td>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                @foreach ($colonne->enfants as $sousColonne)
                                    <td style="text-align: right; font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                                        {{ $matrice[$tableau->id][$ligne->id][$sousColonne->id] ?? '-' }}
                                    </td>
                                @endforeach
                            @else
                                <td style="text-align: right; font-weight: bold; background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                                    {{ $matrice[$tableau->id][$ligne->id][$colonne->id] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                        <td style="text-align: right; font-weight: bold; background-color: #e2e8f0; border: 1px solid #cbd5e1;">
                            {{ $totauxLignes[$tableau->id][$ligne->id] ?? '-' }}
                        </td>
                    </tr>

                    {{-- Sous-lignes --}}
                    @foreach ($ligne->enfants as $sousLigne)
                        <tr>
                            <td style="padding-left: 20px; border: 1px solid #e2e8f0;">
                                  - {{ $sousLigne->nom_ligne }}
                            </td>
                            @foreach ($colonnesRacines as $colonne)
                                @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                    @foreach ($colonne->enfants as $sousColonne)
                                        <td style="text-align: right; border: 1px solid #e2e8f0;">
                                            {{ $matrice[$tableau->id][$sousLigne->id][$sousColonne->id] ?? '-' }}
                                        </td>
                                    @endforeach
                                @else
                                    <td style="text-align: right; border: 1px solid #e2e8f0;">
                                        {{ $matrice[$tableau->id][$sousLigne->id][$colonne->id] ?? '-' }}
                                    </td>
                                @endif
                            @endforeach
                            <td style="text-align: right; font-weight: bold; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                {{ $totauxLignes[$tableau->id][$sousLigne->id] ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Ligne simple --}}
                    <tr>
                        <td style="border: 1px solid #e2e8f0;">
                            {{ $ligne->nom_ligne }}
                        </td>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                @foreach ($colonne->enfants as $sousColonne)
                                    <td style="text-align: right; border: 1px solid #e2e8f0;">
                                        {{ $matrice[$tableau->id][$ligne->id][$sousColonne->id] ?? '-' }}
                                    </td>
                                @endforeach
                            @else
                                <td style="text-align: right; border: 1px solid #e2e8f0;">
                                    {{ $matrice[$tableau->id][$ligne->id][$colonne->id] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                        <td style="text-align: right; font-weight: bold; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            {{ $totauxLignes[$tableau->id][$ligne->id] ?? '-' }}
                        </td>
                    </tr>
                @endif
            @endforeach

            {{-- Total général --}}
            <tr>
                <td style="font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8;">
                    TOTAL GÉNÉRAL
                </td>
                @foreach ($colonnesRacines as $colonne)
                    @if ($colonne->enfants && $colonne->enfants->count() > 0)
                        @foreach ($colonne->enfants as $sousColonne)
                            <td style="text-align: right; font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8;">
                                {{ $totauxColonnes[$tableau->id][$sousColonne->id] ?? '-' }}
                            </td>
                        @endforeach
                    @else
                        <td style="text-align: right; font-weight: bold; background-color: #cbd5e1; border: 1px solid #94a3b8;">
                            {{ $totauxColonnes[$tableau->id][$colonne->id] ?? '-' }}
                        </td>
                    @endif
                @endforeach
                <td style="text-align: right; font-weight: bold; background-color: #94a3b8; color: #ffffff; border: 1px solid #64748b;">
                    {{ $totalGeneral[$tableau->id] ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>
    <table><tr><th></th></tr></table>
@endforeach
