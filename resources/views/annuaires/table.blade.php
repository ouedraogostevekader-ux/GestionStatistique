<div class="card-body">
    <div class="table-responsive">
        @php
            $isTotalOrEnsemble = fn($nom) => in_array(mb_strtolower(trim($nom)), ['ensemble', 'total', 'total général', 'totaux']);
            $lignesRacines = $tableau->lignesTableau->whereNull('parent_id')
                ->filter(fn($l) => $l->ordre != 1 && !$isTotalOrEnsemble($l->nom_ligne));
            $colonnesRacines = $tableau->colonnesTableau->whereNull('parent_id');
            $headerLigne = $tableau->lignesTableau->firstWhere('ordre', 1);
            $headerTitre = $headerLigne ? $headerLigne->nom_ligne : 'Libellé';
            $hasSousColonnes = $colonnesRacines->contains(fn($c) => $c->enfants && $c->enfants->count() > 0);
        @endphp
        <table class="table table-bordered table-annuaire">
            <thead class="table-light">
                <tr>
                    <th @if($hasSousColonnes) rowspan="2" @endif class="align-middle">
                        {{ $headerTitre }}
                    </th>
                    @foreach ($colonnesRacines as $colonne)
                        @if ($colonne->enfants && $colonne->enfants->count() > 0)
                            <th colspan="{{ $colonne->enfants->count() }}" class="text-center">
                                {{ $colonne->colonne_ligne }}
                            </th>
                        @else
                            <th @if($hasSousColonnes) rowspan="2" @endif class="text-center align-middle">
                                {{ $colonne->colonne_ligne }}
                            </th>
                        @endif
                    @endforeach
                    <th @if($hasSousColonnes) rowspan="2" @endif class="text-center align-middle">
                        Total
                    </th>
                </tr>
                @if ($hasSousColonnes)
                    <tr>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                @foreach ($colonne->enfants as $sousColonne)
                                    <th class="text-center">{{ $sousColonne->colonne_ligne }}</th>
                                @endforeach
                            @endif
                        @endforeach
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach ($lignesRacines as $ligne)
                    <tr>
                        <td class="fw-bold">{{ $ligne->nom_ligne }}</td>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                @foreach ($colonne->enfants as $sousColonne)
                                    <td class="text-center">
                                        {{ $matrice[$tableau->id][$ligne->id][$sousColonne->id] ?? '-' }}
                                    </td>
                                @endforeach
                            @else
                                <td class="text-center">
                                    {{ $matrice[$tableau->id][$ligne->id][$colonne->id] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                        <td class="total-ligne text-center fw-bold">
                            {{ isset($totauxLignes[$tableau->id][$ligne->id]) ? number_format($totauxLignes[$tableau->id][$ligne->id], 2, ',', ' ') : '0,00' }}
                        </td>
                    </tr>

                    {{-- Sous-lignes --}}
                    @if ($ligne->enfants && $ligne->enfants->count() > 0)
                        @foreach ($ligne->enfants as $sousLigne)
                            <tr class="table-secondary">
                                <td class="ps-4">{{ $sousLigne->nom_ligne }}</td>
                                @foreach ($colonnesRacines as $colonne)
                                    @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                        @foreach ($colonne->enfants as $sousColonne)
                                            <td class="text-center">
                                                {{ $matrice[$tableau->id][$sousLigne->id][$sousColonne->id] ?? '-' }}
                                            </td>
                                        @endforeach
                                    @else
                                        <td class="text-center">
                                            {{ $matrice[$tableau->id][$sousLigne->id][$colonne->id] ?? '-' }}
                                        </td>
                                    @endif
                                @endforeach
                                <td class="total-ligne text-center">
                                    {{ isset($totauxLignes[$tableau->id][$sousLigne->id]) ? number_format($totauxLignes[$tableau->id][$sousLigne->id], 2, ',', ' ') : '0,00' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                {{-- Ligne Ensemble (Total) --}}
                <tr class="table-primary fw-bold">
                    <td>Ensemble</td>
                    @foreach ($colonnesRacines as $colonne)
                        @if ($colonne->enfants && $colonne->enfants->count() > 0)
                            @foreach ($colonne->enfants as $sousColonne)
                                <td class="text-center">
                                    <span class="total-colonne">
                                        {{ isset($totauxColonnes[$tableau->id][$sousColonne->id]) ? number_format($totauxColonnes[$tableau->id][$sousColonne->id], 2, ',', ' ') : '0,00' }}
                                    </span>
                                </td>
                            @endforeach
                        @else
                            <td class="text-center">
                                <span class="total-colonne">
                                    {{ isset($totauxColonnes[$tableau->id][$colonne->id]) ? number_format($totauxColonnes[$tableau->id][$colonne->id], 2, ',', ' ') : '0,00' }}
                                </span>
                            </td>
                        @endif
                    @endforeach
                    <td class="text-center">
                        <span class="total-general">
                            {{ isset($totalGeneral[$tableau->id]) ? number_format($totalGeneral[$tableau->id], 2, ',', ' ') : '0,00' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="text-muted">{{ $tableau->description ?? '' }}</p>
    </div>
</div>
