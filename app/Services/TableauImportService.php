<?php

namespace App\Services;

use App\Models\Tableau;
use App\Models\Assignation;
use App\Models\Structure;
use App\Models\Cellule;
use App\Models\LigneTableau;
use App\Models\ColonneTableau;
use App\Models\SoumissionTableau;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TableauImportService
{
    /**
     * Analyse, valide et importe un fichier Excel dans soumissions_tableau
     *
     * @param UploadedFile|string $file
     * @param Tableau $tableau
     * @param Assignation $assignation
     * @param Structure $structure
     * @param bool $ecraser
     * @return array Résultat contenant 'success', 'imported_count', 'errors', 'message'
     */
    public function importer($file, Tableau $tableau, Assignation $assignation, Structure $structure, bool $ecraser = true): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => ['Le fichier spécifié est introuvable sur le serveur.'],
                'message' => 'Fichier introuvable.'
            ];
        }

        try {
            // Lecture du fichier avec PhpSpreadsheet
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(false); // Permet de lire les valeurs calculées et métadonnées
            $spreadsheet = $reader->load($filePath);
        } catch (\Throwable $e) {
            Log::error("Erreur lecture fichier Excel pour tableau {$tableau->id} : " . $e->getMessage());
            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => ['Format de fichier invalide ou fichier corrompu. Veuillez téléverser un classeur Excel valide (.xlsx ou .xls).'],
                'message' => 'Fichier illisible.'
            ];
        }

        // Vérification de la présence de la feuille de saisie
        $sheetSaisie = $spreadsheet->getSheetByName('Saisie_Donnees') ?? $spreadsheet->getSheet(0);
        if (!$sheetSaisie) {
            $spreadsheet->disconnectWorksheets();
            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => ['Feuille de données statistique introuvable dans le classeur.'],
                'message' => 'Structure de classeur incorrecte.'
            ];
        }

        $sheetMeta = $spreadsheet->getSheetByName('_METADATA');

        // Validation d'intégrité via _METADATA si présent
        if ($sheetMeta) {
            $metaTableauId = trim((string) $sheetMeta->getCell('L1')->getValue());
            if (!empty($metaTableauId) && $metaTableauId !== $tableau->id) {
                $spreadsheet->disconnectWorksheets();
                return [
                    'success' => false,
                    'imported_count' => 0,
                    'errors' => [
                        "Incohérence détectée : Ce fichier canevas a été généré pour un autre tableau statistique (ID: {$metaTableauId}). Téléversement annulé par mesure de sécurité."
                    ],
                    'message' => 'Discordance de tableau.'
                ];
            }
        }

        // Extraction et validation des cellules
        $donneesAInserer = [];
        $errors = [];

        if ($sheetMeta) {
            // Extraction via la table de mapping exacte _METADATA
            $highestRow = $sheetMeta->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $coord = trim((string) $sheetMeta->getCell([1, $row])->getValue());
                $celluleId = trim((string) $sheetMeta->getCell([2, $row])->getValue());
                $ligneNom = trim((string) $sheetMeta->getCell([4, $row])->getValue());
                $colonneNom = trim((string) $sheetMeta->getCell([6, $row])->getValue());

                if (empty($coord) || empty($celluleId)) {
                    continue;
                }

                // Lire la valeur dans la feuille de saisie
                $valeurBrute = $sheetSaisie->getCell($coord)->getCalculatedValue();

                $resultatCellule = $this->traiterValeurCellule($valeurBrute, $coord, $ligneNom, $colonneNom);

                if ($resultatCellule['has_error']) {
                    $errors[] = $resultatCellule['error'];
                } elseif ($resultatCellule['valeur'] !== null) {
                    $donneesAInserer[$celluleId] = $resultatCellule['valeur'];
                }
            }
        } else {
            // Mode dégradé / géométrique : parcours direct de la feuille sans feuille _METADATA
            $extractionGeo = $this->extraireDonneesGeometriques($sheetSaisie, $tableau);
            $errors = array_merge($errors, $extractionGeo['errors']);
            $donneesAInserer = $extractionGeo['donnees'];
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        // Si des erreurs de format ou de type numérique ont été détectées : ROLLBACK immédiat
        if (!empty($errors)) {
            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => $errors,
                'message' => count($errors) . " erreur(s) détectée(s) dans le fichier. L'enregistrement a été intégralement annulé pour protéger l'intégrité des statistiques."
            ];
        }

        if (empty($donneesAInserer)) {
            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => ['Aucune donnée numérique n\'a été trouvée dans les cellules de saisie du classeur.'],
                'message' => 'Fichier sans données.'
            ];
        }

        // Persistance transactionnelle sécurisée
        DB::beginTransaction();
        try {
            $importedCount = 0;

            foreach ($donneesAInserer as $celluleId => $valeurNum) {
                // Recherche de la cellule pour vérifier qu'elle appartient bien au tableau
                $cellule = Cellule::find($celluleId);
                if (!$cellule) {
                    continue;
                }

                SoumissionTableau::updateOrCreate(
                    [
                        'tableau_id' => $tableau->id,
                        'structure_id' => $structure->id,
                        'assignation_id' => $assignation->id,
                        'cellule_id' => $celluleId
                    ],
                    [
                        'id' => (string) Str::ulid(),
                        'valeur' => $valeurNum
                    ]
                );

                $importedCount++;
            }

            // Mise à jour automatique du statut de l'assignation : Transmis / En attente de validation
            $assignation->update(['statut' => 2]);

            DB::commit();

            return [
                'success' => true,
                'imported_count' => $importedCount,
                'errors' => [],
                'message' => "{$importedCount} valeur(s) statistique(s) importée(s) avec succès. L'assignation est désormais transmise et en attente de validation."
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Erreur transaction import tableau {$tableau->id} : " . $e->getMessage());

            return [
                'success' => false,
                'imported_count' => 0,
                'errors' => ["Une erreur imprévue est survenue lors de l'enregistrement en base de données : " . $e->getMessage()],
                'message' => 'Erreur technique lors de la persistance.'
            ];
        }
    }

    /**
     * Valide et normalise une valeur de cellule
     */
    protected function traiterValeurCellule($valeurBrute, string $coord, string $ligneNom, string $colonneNom): array
    {
        if ($valeurBrute === null) {
            return ['has_error' => false, 'valeur' => null];
        }

        $valeurStr = trim((string) $valeurBrute);

        // Si la cellule est vide ou marquée d'un tiret d'omission
        if ($valeurStr === '' || $valeurStr === '-') {
            return ['has_error' => false, 'valeur' => null];
        }

        // Remplacement des espaces insécables et virgules décimales francophones
        $valeurNettoyee = str_replace(["\xc2\xa0", " ", "\t"], '', $valeurStr);
        $valeurNettoyee = str_replace(',', '.', $valeurNettoyee);

        // Validation stricte du type numérique
        if (!is_numeric($valeurNettoyee) || is_nan((float) $valeurNettoyee) || is_infinite((float) $valeurNettoyee)) {
            return [
                'has_error' => true,
                'error' => "Cellule {$coord} [Ligne: \"{$ligneNom}\", Colonne: \"{$colonneNom}\"] : La valeur '{$valeurStr}' n'est pas un nombre valide.",
                'valeur' => null
            ];
        }

        return [
            'has_error' => false,
            'valeur' => (float) $valeurNettoyee
        ];
    }

    /**
     * Fallback pour extraire géométriquement les cellules lorsque _METADATA n'est pas disponible
     */
    protected function extraireDonneesGeometriques($sheet, Tableau $tableau): array
    {
        $donnees = [];
        $errors = [];

        // Récupérer les lignes et colonnes du tableau
        $colonnesFeuilles = [];
        $colonnesRacines = ColonneTableau::where('tableau_id', $tableau->id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with(['enfants' => fn($q) => $q->orderBy('ordre')])
            ->get();

        foreach ($colonnesRacines as $c) {
            if ($c->enfants->count() > 0) {
                foreach ($c->enfants as $sc) {
                    $colonnesFeuilles[] = $sc;
                }
            } else {
                $colonnesFeuilles[] = $c;
            }
        }

        $lignesDonnees = [];
        $lignesRacines = LigneTableau::where('tableau_id', $tableau->id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with(['enfants' => fn($q) => $q->orderBy('ordre')])
            ->get();

        $isLigneIgnoree = function ($ligne) {
            return $ligne->ordre == 1 || in_array(mb_strtolower(trim($ligne->nom_ligne)), ['ensemble', 'total', 'total général', 'totaux']);
        };

        foreach ($lignesRacines as $l) {
            if ($isLigneIgnoree($l)) {
                continue;
            }
            $lignesDonnees[] = $l;
            foreach ($l->enfants as $sl) {
                $lignesDonnees[] = $sl;
            }
        }

        // Recherche de la première ligne de données dans la feuille (cherche la première correspondance de ligne)
        $highestRow = $sheet->getHighestRow();
        $startRow = 8;

        for ($r = 1; $r <= min(20, $highestRow); $r++) {
            $cellVal = trim((string) $sheet->getCell([1, $r])->getValue());
            if (!empty($lignesDonnees) && mb_strtolower($cellVal) === mb_strtolower(trim($lignesDonnees[0]->nom_ligne))) {
                $startRow = $r;
                break;
            }
        }

        $currentRow = $startRow;
        foreach ($lignesDonnees as $ligneObj) {
            if ($currentRow > $highestRow) {
                break;
            }

            $colIdx = 2;
            foreach ($colonnesFeuilles as $colLeaf) {
                $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . $currentRow;
                $valeurBrute = $sheet->getCell($cellCoord)->getCalculatedValue();

                $cellule = Cellule::firstOrCreate(
                    ['ligne_id' => $ligneObj->id, 'colonne_id' => $colLeaf->id],
                    ['id' => (string) Str::ulid()]
                );

                $res = $this->traiterValeurCellule($valeurBrute, $cellCoord, $ligneObj->nom_ligne, $colLeaf->colonne_ligne);
                if ($res['has_error']) {
                    $errors[] = $res['error'];
                } elseif ($res['valeur'] !== null) {
                    $donnees[$cellule->id] = $res['valeur'];
                }

                $colIdx++;
            }

            $currentRow++;
        }

        return ['donnees' => $donnees, 'errors' => $errors];
    }
}
