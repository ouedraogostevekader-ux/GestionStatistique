<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    {{-- <th rowspan="2">Libellé</th> --}}
                    @foreach ($tableau->lignesTableau as $ligne)
                        @if ($ligne->ordre == 1)
                            <th>{{ $ligne->nom_ligne }}
                            </th>
                        @endif
                    @endforeach
                    @foreach ($tableau->colonnesTableau as $colonne)
                        @if ($colonne->enfants->count() > 0)
                            <th colspan="{{ $colonne->enfants->count() }}" class="text-center">
                                {{ $colonne->colonne_ligne }}
                            </th>
                        @else
                            <th rowspan="2" class="text-center">{{ $colonne->colonne_ligne }}
                            </th>
                        @endif
                    @endforeach
                    <th rowspan="2" class="text-center">Total</th>
                </tr>
                <tr>
                    @foreach ($tableau->colonnesTableau as $colonne)
                        @foreach ($colonne->enfants as $sousColonne)
                            <th class="text-center">{{ $sousColonne->colonne_ligne }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tableau->lignesTableau as $ligne)
                    <tr>
                        @if ($ligne->ordre != 1)
                            <td class="fw-bold">{{ $ligne->nom_ligne }}</td>
                            @foreach ($tableau->colonnesTableau as $colonne)
                                @if ($colonne->enfants->count() > 0)
                                    @foreach ($colonne->enfants as $sousColonne)
                                        @php
                                            $cellule = \App\Models\Cellule::where('ligne_id', $ligne->id)
                                                ->where('colonne_id', $sousColonne->id)
                                                ->first();
                                            $valeur = $cellule
                                                ? \App\Models\SoumissionTableau::firstWhere('cellule_id', $cellule->id)
                                                    ?->valeur
                                                : '-';
                                        @endphp
                                        <td class="text-center">{{ $valeur ?? '-' }}</td>
                                    @endforeach
                                @else
                                    @php
                                        $cellule = \App\Models\Cellule::where('ligne_id', $ligne->id)
                                            ->where('colonne_id', $colonne->id)
                                            ->first();
                                        $valeur = $cellule
                                            ? \App\Models\SoumissionTableau::firstWhere('cellule_id', $cellule->id)
                                                ?->valeur
                                            : '-';
                                    @endphp
                                    <td class="text-center">{{ $valeur ?? '-' }}</td>
                                @endif
                            @endforeach
                            <td class="total-ligne text-center fw-bold">0.00</td>
                        @endif
                    </tr>

                    {{-- Sous-lignes --}}
                    @foreach ($ligne->enfants as $sousLigne)
                        <tr class="table-secondary">
                            <td class="ps-4">{{ $sousLigne->nom_ligne }}</td>
                            @foreach ($tableau->colonnesTableau as $colonne)
                                @if ($colonne->enfants->count() > 0)
                                    @foreach ($colonne->enfants as $sousColonne)
                                        @php
                                            $cellule = \App\Models\Cellule::where('ligne_id', $sousLigne->id)
                                                ->where('colonne_id', $sousColonne->id)
                                                ->first();
                                            $valeur = $cellule
                                                ? \App\Models\SoumissionTableau::firstWhere('cellule_id', $cellule->id)
                                                    ?->valeur
                                                : '-';
                                        @endphp
                                        <td class="text-center">{{ $valeur ?? '-' }}</td>
                                    @endforeach
                                @else
                                    @php
                                        $cellule = \App\Models\Cellule::where('ligne_id', $sousLigne->id)
                                            ->where('colonne_id', $colonne->id)
                                            ->first();
                                        $valeur = $cellule
                                            ? \App\Models\SoumissionTableau::firstWhere('cellule_id', $cellule->id)
                                                ?->valeur
                                            : '-';
                                    @endphp
                                    <td class="text-center">{{ $valeur ?? '-' }}</td>
                                @endif
                            @endforeach
                            <td class="total-ligne text-center">0.00</td>
                        </tr>
                    @endforeach
                @endforeach
                {{-- Ligne Ensemble (Total) --}}
                <tr class="table-primary fw-bold">
                    <td>Ensemble</td>
                    @foreach ($tableau->colonnesTableau as $colonne)
                        @if ($colonne->enfants->count() > 0)
                            @foreach ($colonne->enfants as $sousColonne)
                                <td class="text-center">
                                    <span class="total-colonne">0.00</span>
                                </td>
                            @endforeach
                        @else
                            <td class="text-center">
                                <span class="total-colonne">0.00</span>
                            </td>
                        @endif
                    @endforeach
                    <td class="text-center">
                        <span class="total-general">0.00</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="text-muted">{{ $tableau->description ?? '' }}</p>
    </div>
</div>
