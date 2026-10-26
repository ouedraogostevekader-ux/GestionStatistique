<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Annuaire Statistique - {{ $version->annee ?? '' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            color: #2c3e50;
            line-height: 1.3;
        }
        .header-section {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #003366;
            padding-bottom: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .ministere-block {
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            color: #1a365d;
            line-height: 1.25;
        }
        .devise-block {
            text-align: right;
            font-size: 9px;
            font-weight: bold;
            color: #1a365d;
            line-height: 1.25;
        }
        .doc-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .doc-title h1 {
            font-size: 16px;
            font-weight: bold;
            color: #003366;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title .edition {
            font-size: 12px;
            color: #4a5568;
            font-weight: bold;
        }
        .tableau-wrapper {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .tableau-header {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            margin-bottom: 6px;
            border-left: 4px solid #003366;
        }
        .tableau-categorie {
            font-size: 8.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .tableau-titre {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        table.table-annuaire {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 10px;
        }
        table.table-annuaire th,
        table.table-annuaire td {
            border: 0.5px solid #94a3b8;
            padding: 4px 6px;
            text-align: left;
        }
        table.table-annuaire thead th {
            background-color: #e2e8f0;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        table.table-annuaire tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .ligne-parent {
            font-weight: bold;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .sous-ligne {
            padding-left: 14px !important;
            color: #334155;
        }
        .col-valeur {
            text-align: right !important;
            font-variant-numeric: tabular-nums;
        }
        .col-total {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: right !important;
            color: #0f172a;
        }
        .ligne-totaux {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            border-top: 1.5px solid #475569 !important;
            color: #0f172a;
        }
        .page-break {
            page-break-after: always;
        }
        .footer-info {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header-section">
        <table class="header-table">
            <tr>
                <td style="width: 50%;" class="ministere-block">
                    BURKINA FASO<br>
                    MINISTÈRE DES INFRASTRUCTURES<br>
                    SECRETARIAT GÉNÉRAL<br>
                    DIRECTION GÉNÉRALE DES ÉTUDES ET DES STATISTIQUES SECTORIELLES (DGESS)
                </td>
                <td style="width: 50%;" class="devise-block">
                    BURKINA FASO<br>
                    <em>La Patrie ou la Mort, nous Vaincrons</em><br>
                    Date : {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        <div class="doc-title">
            <h1>Annuaire Statistique</h1>
            <div class="edition">Édition {{ $version->annee ?? '' }} (Version : {{ $version->code ?? 'N/A' }})</div>
        </div>
    </div>

    @forelse ($tableaux as $tableau)
        @php
            $isTotalOrEnsemble = fn($nom) => in_array(mb_strtolower(trim($nom)), ['ensemble', 'total', 'total général', 'totaux']);
            $lignesRacines = $tableau->lignesTableau->whereNull('parent_id')
                ->filter(fn($l) => $l->ordre != 1 && !$isTotalOrEnsemble($l->nom_ligne));
            $colonnesRacines = $tableau->colonnesTableau->whereNull('parent_id');
            $headerLigne = $tableau->lignesTableau->firstWhere('ordre', 1);
            $headerTitre = $headerLigne ? $headerLigne->nom_ligne : 'Libellé';
            $hasSousColonnes = $colonnesRacines->contains(fn($c) => $c->enfants && $c->enfants->count() > 0);
        @endphp

        <div class="tableau-wrapper">
            <div class="tableau-header">
                @if ($tableau->categorie)
                    <div class="tableau-categorie">Catégorie : {{ $tableau->categorie->libelle }}</div>
                @endif
                <div class="tableau-titre">{{ $tableau->libelle }}</div>
            </div>

            <table class="table-annuaire">
                <thead>
                    <tr>
                        <th @if($hasSousColonnes) rowspan="2" @endif style="text-align: left;">
                            {{ $headerTitre }}
                        </th>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                <th colspan="{{ $colonne->enfants->count() }}">
                                    {{ $colonne->colonne_ligne }}
                                </th>
                            @else
                                <th @if($hasSousColonnes) rowspan="2" @endif>
                                    {{ $colonne->colonne_ligne }}
                                </th>
                            @endif
                        @endforeach
                        <th @if($hasSousColonnes) rowspan="2" @endif class="col-total">
                            Total
                        </th>
                    </tr>
                    @if ($hasSousColonnes)
                        <tr>
                            @foreach ($colonnesRacines as $colonne)
                                @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                    @foreach ($colonne->enfants as $sousColonne)
                                        <th>{{ $sousColonne->colonne_ligne }}</th>
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
                            <tr class="ligne-parent">
                                <td>{{ $ligne->nom_ligne }}</td>
                                @foreach ($colonnesRacines as $colonne)
                                    @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                        @foreach ($colonne->enfants as $sousColonne)
                                            <td class="col-valeur">
                                                {{ $matrice[$tableau->id][$ligne->id][$sousColonne->id] ?? '-' }}
                                            </td>
                                        @endforeach
                                    @else
                                        <td class="col-valeur">
                                            {{ $matrice[$tableau->id][$ligne->id][$colonne->id] ?? '-' }}
                                        </td>
                                    @endif
                                @endforeach
                                <td class="col-total">
                                    {{ $totauxLignes[$tableau->id][$ligne->id] ?? '-' }}
                                </td>
                            </tr>

                            {{-- Sous-Lignes --}}
                            @foreach ($ligne->enfants as $sousLigne)
                                <tr>
                                    <td class="sous-ligne">&bull; {{ $sousLigne->nom_ligne }}</td>
                                    @foreach ($colonnesRacines as $colonne)
                                        @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                            @foreach ($colonne->enfants as $sousColonne)
                                                <td class="col-valeur">
                                                    {{ $matrice[$tableau->id][$sousLigne->id][$sousColonne->id] ?? '-' }}
                                                </td>
                                            @endforeach
                                        @else
                                            <td class="col-valeur">
                                                {{ $matrice[$tableau->id][$sousLigne->id][$colonne->id] ?? '-' }}
                                            </td>
                                        @endif
                                    @endforeach
                                    <td class="col-total">
                                        {{ $totauxLignes[$tableau->id][$sousLigne->id] ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Ligne simple sans sous-lignes --}}
                            <tr>
                                <td>{{ $ligne->nom_ligne }}</td>
                                @foreach ($colonnesRacines as $colonne)
                                    @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                        @foreach ($colonne->enfants as $sousColonne)
                                            <td class="col-valeur">
                                                {{ $matrice[$tableau->id][$ligne->id][$sousColonne->id] ?? '-' }}
                                            </td>
                                        @endforeach
                                    @else
                                        <td class="col-valeur">
                                            {{ $matrice[$tableau->id][$ligne->id][$colonne->id] ?? '-' }}
                                        </td>
                                    @endif
                                @endforeach
                                <td class="col-total">
                                    {{ $totauxLignes[$tableau->id][$ligne->id] ?? '-' }}
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Ligne des totaux généraux --}}
                    <tr class="ligne-totaux">
                        <td>TOTAL GÉNÉRAL</td>
                        @foreach ($colonnesRacines as $colonne)
                            @if ($colonne->enfants && $colonne->enfants->count() > 0)
                                @foreach ($colonne->enfants as $sousColonne)
                                    <td class="col-total">
                                        {{ $totauxColonnes[$tableau->id][$sousColonne->id] ?? '-' }}
                                    </td>
                                @endforeach
                            @else
                                <td class="col-total">
                                    {{ $totauxColonnes[$tableau->id][$colonne->id] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                        <td class="col-total" style="background-color: #cbd5e1;">
                            {{ $totalGeneral[$tableau->id] ?? '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>Aucun tableau disponible pour cette sélection.</p>
        </div>
    @endforelse

    <div class="footer-info">
        Plateforme de Gestion des Données Statistiques — Ministère des Infrastructures (MID) / DGESS
    </div>

</body>
</html>
