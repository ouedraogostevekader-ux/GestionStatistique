<?php

namespace App\Services;

use App\Models\Assignation;
use App\Models\Cellule;
use App\Models\SoumissionTableau;
use App\Models\Tableau;
use App\Models\Version;
use Illuminate\Support\Collection;

class AnnuaireService
{
    /**
     * Charge et agrège les données de l'annuaire pour une version donnée sous forme de matrice optimisée.
     * Élimine toutes les requêtes N+1 et calcule les totaux en mémoire.
     *
     * @param string|null $versionId
     * @return array
     */
    public function getDonneesAnnuaire(?string $versionId = null): array
    {
        $versions = Version::orderByDesc('annee')->get();

        if (!$versionId && $versions->isNotEmpty()) {
            $versionId = $versions->first()->id;
        }

        $versionActive = $versionId ? Version::find($versionId) : null;

        if (!$versionActive) {
            return [
                'versions' => $versions,
                'version' => null,
                'tableaux' => collect(),
                'matrice' => [],
                'totauxLignes' => [],
                'totauxColonnes' => [],
                'totalGeneral' => [],
                'cellulesMap' => [],
            ];
        }

        // 1. Récupérer les assignations pour cette version
        $assignations = Assignation::where('version_id', $versionId)->get();
        $assignationIds = $assignations->pluck('id')->filter()->unique();

        // 2. Récupérer les tableaux assignés avec leurs lignes et colonnes ordonnées (racines uniquement)
        $tableaux = Tableau::whereIn('id', function ($query) use ($assignationIds) {
            $query->select('tableau_id')
                ->from('assignations')
                ->whereIn('id', $assignationIds);
        })
        ->with([
            'categorie',
            'lignesTableau' => fn($q) => $q->whereNull('parent_id')->orderBy('ordre'),
            'lignesTableau.enfants' => fn($q) => $q->orderBy('ordre'),
            'colonnesTableau' => fn($q) => $q->whereNull('parent_id')->orderBy('ordre'),
            'colonnesTableau.enfants' => fn($q) => $q->orderBy('ordre'),
        ])
        ->get();

        // Si aucun tableau n'est assigné, chercher les tableaux ayant des soumissions sur ces assignations
        if ($tableaux->isEmpty() && $assignationIds->isNotEmpty()) {
            $tableaux = Tableau::whereIn('id', function ($query) use ($assignationIds) {
                $query->select('tableau_id')
                    ->from('soumissions_tableau')
                    ->whereIn('assignation_id', $assignationIds);
            })
            ->with([
                'categorie',
                'lignesTableau' => fn($q) => $q->whereNull('parent_id')->orderBy('ordre'),
                'lignesTableau.enfants' => fn($q) => $q->orderBy('ordre'),
                'colonnesTableau' => fn($q) => $q->whereNull('parent_id')->orderBy('ordre'),
                'colonnesTableau.enfants' => fn($q) => $q->orderBy('ordre'),
            ])
            ->get();
        }

        // 3. Collecter tous les IDs de lignes et colonnes
        $allLigneIds = [];
        $allColonneIds = [];

        foreach ($tableaux as $tableau) {
            foreach ($tableau->lignesTableau as $ligne) {
                $allLigneIds[] = $ligne->id;
                foreach ($ligne->enfants as $sousLigne) {
                    $allLigneIds[] = $sousLigne->id;
                }
            }

            foreach ($tableau->colonnesTableau as $colonne) {
                $allColonneIds[] = $colonne->id;
                foreach ($colonne->enfants as $sousColonne) {
                    $allColonneIds[] = $sousColonne->id;
                }
            }
        }

        $allLigneIds = array_unique($allLigneIds);
        $allColonneIds = array_unique($allColonneIds);

        // 4. Charger toutes les cellules correspondantes en UNE seule requête
        $cellules = Cellule::whereIn('ligne_id', $allLigneIds)
            ->whereIn('colonne_id', $allColonneIds)
            ->get();

        $cellulesMap = [];
        $celluleIds = [];

        foreach ($cellules as $cellule) {
            $cellulesMap[$cellule->ligne_id][$cellule->colonne_id] = $cellule->id;
            $celluleIds[] = $cellule->id;
        }

        // 5. Charger toutes les soumissions correspondantes en UNE seule requête
        $soumissions = SoumissionTableau::whereIn('assignation_id', $assignationIds)
            ->whereIn('cellule_id', $celluleIds)
            ->get(['cellule_id', 'valeur', 'assignation_id', 'tableau_id']);

        $valeursParCellule = [];
        foreach ($soumissions as $soumission) {
            $valeursParCellule[$soumission->cellule_id] = $soumission->valeur;
        }

        // 6. Construire la matrice et calculer les totaux pour chaque tableau
        $matrice = [];
        $totauxLignes = [];
        $totauxColonnes = [];
        $totalGeneral = [];

        foreach ($tableaux as $tableau) {
            $tabId = $tableau->id;
            $matrice[$tabId] = [];
            $totauxLignes[$tabId] = [];
            $totauxColonnes[$tabId] = [];
            $totalGeneral[$tabId] = 0.0;

            // Déterminer les colonnes "feuilles" (sans sous-colonnes)
            $colonnesFeuilles = [];
            foreach ($tableau->colonnesTableau as $colonne) {
                if ($colonne->enfants && $colonne->enfants->count() > 0) {
                    foreach ($colonne->enfants as $sousCol) {
                        $colonnesFeuilles[] = $sousCol;
                    }
                } else {
                    $colonnesFeuilles[] = $colonne;
                }
            }

            foreach ($colonnesFeuilles as $col) {
                $totauxColonnes[$tabId][$col->id] = 0.0;
            }

            // Calculer par ligne et sous-ligne
            foreach ($tableau->lignesTableau as $ligne) {
                $hasChildren = $ligne->enfants && $ligne->enfants->count() > 0;
                $totauxLignes[$tabId][$ligne->id] = 0.0;

                // Si pas d'enfants, on calcule directement pour la ligne
                if (!$hasChildren) {
                    foreach ($colonnesFeuilles as $col) {
                        $cellId = $cellulesMap[$ligne->id][$col->id] ?? null;
                        $val = $cellId && isset($valeursParCellule[$cellId]) ? $valeursParCellule[$cellId] : null;

                        $matrice[$tabId][$ligne->id][$col->id] = $val !== null ? $val : '-';

                        if (is_numeric($val)) {
                            $numVal = (float)$val;
                            $totauxLignes[$tabId][$ligne->id] += $numVal;
                            $totauxColonnes[$tabId][$col->id] += $numVal;
                            $totalGeneral[$tabId] += $numVal;
                        }
                    }
                } else {
                    // Si des sous-lignes existent, on calcule pour chaque sous-ligne et on agrège sur le parent
                    $totalParent = 0.0;
                    $parentColSums = [];
                    $parentColHasVal = [];

                    foreach ($colonnesFeuilles as $col) {
                        $parentColSums[$col->id] = 0.0;
                        $parentColHasVal[$col->id] = false;
                    }

                    foreach ($ligne->enfants as $sousLigne) {
                        $totauxLignes[$tabId][$sousLigne->id] = 0.0;

                        foreach ($colonnesFeuilles as $col) {
                            $cellId = $cellulesMap[$sousLigne->id][$col->id] ?? null;
                            $val = $cellId && isset($valeursParCellule[$cellId]) ? $valeursParCellule[$cellId] : null;

                            $matrice[$tabId][$sousLigne->id][$col->id] = $val !== null ? $val : '-';

                            if (is_numeric($val)) {
                                $numVal = (float)$val;
                                $totauxLignes[$tabId][$sousLigne->id] += $numVal;
                                $totauxColonnes[$tabId][$col->id] += $numVal;
                                $totalGeneral[$tabId] += $numVal;
                                $totalParent += $numVal;

                                $parentColSums[$col->id] += $numVal;
                                $parentColHasVal[$col->id] = true;
                            }
                        }
                    }

                    // Déterminer la valeur affichée pour chaque colonne de la ligne parente
                    foreach ($colonnesFeuilles as $col) {
                        $cellParentId = $cellulesMap[$ligne->id][$col->id] ?? null;
                        $valParent = $cellParentId && isset($valeursParCellule[$cellParentId]) ? $valeursParCellule[$cellParentId] : null;

                        if ($valParent !== null) {
                            $matrice[$tabId][$ligne->id][$col->id] = $valParent;
                        } elseif ($parentColHasVal[$col->id]) {
                            $matrice[$tabId][$ligne->id][$col->id] = $parentColSums[$col->id];
                        } else {
                            $matrice[$tabId][$ligne->id][$col->id] = '-';
                        }
                    }

                    $totauxLignes[$tabId][$ligne->id] = $totalParent;
                }
            }
        }

        return [
            'versions' => $versions,
            'version' => $versionActive,
            'tableaux' => $tableaux,
            'matrice' => $matrice,
            'totauxLignes' => $totauxLignes,
            'totauxColonnes' => $totauxColonnes,
            'totalGeneral' => $totalGeneral,
            'cellulesMap' => $cellulesMap,
            'assignations' => $assignationIds,
        ];
    }
}
