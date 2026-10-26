<?php

namespace App\Exports;

use App\Models\Tableau;
use App\Models\Assignation;
use App\Models\Cellule;
use App\Models\SoumissionTableau;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TableauCanevasExport
{
    protected Tableau $tableau;
    protected Assignation $assignation;
    protected array $soumissions = [];

    public function __construct(Tableau $tableau, Assignation $assignation)
    {
        $this->tableau = $tableau;
        $this->assignation = $assignation;

        // Pré-charger les soumissions existantes pour pré-remplir le canevas si déjà saisies
        $this->soumissions = SoumissionTableau::where('assignation_id', $assignation->id)
            ->where('tableau_id', $tableau->id)
            ->pluck('valeur', 'cellule_id')
            ->toArray();
    }

    /**
     * Génère et télécharge le fichier Excel du canevas
     */
    public function download(string $filename): BinaryFileResponse
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'canevas_excel_') . '.xlsx';
        $spreadsheet = $this->buildSpreadsheet();

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Construit l'objet Spreadsheet en mémoire
     */
    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('DGESS - Ministère des Infrastructures')
            ->setTitle('Canevas Statistique - ' . $this->tableau->libelle);

        // 1. Feuille principale de saisie
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Saisie_Donnees');
        $sheet->setShowGridLines(true);

        // Structure des colonnes et des lignes
        $colonnesRacines = $this->tableau->colonnesTableau()
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with(['enfants' => fn($q) => $q->orderBy('ordre')])
            ->get();

        $lignesRacines = $this->tableau->lignesTableau()
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with(['enfants' => fn($q) => $q->orderBy('ordre')])
            ->get();

        // Récupérer les colonnes terminales (feuilles) pour la saisie
        $colonnesFeuilles = [];
        foreach ($colonnesRacines as $col) {
            if ($col->enfants->count() > 0) {
                foreach ($col->enfants as $sousCol) {
                    $colonnesFeuilles[] = $sousCol;
                }
            } else {
                $colonnesFeuilles[] = $col;
            }
        }

        // Récupérer les lignes terminales (données)
        // La première ligne avec ordre = 1 sert traditionnellement d'intitulé de colonne
        $ligneIntitule = $lignesRacines->firstWhere('ordre', 1);
        $libelleLigneHeader = $ligneIntitule ? $ligneIntitule->nom_ligne : 'Libellé / Indicateur';

        $isLigneIgnoree = function ($ligne) {
            return $ligne->ordre == 1 || in_array(mb_strtolower(trim($ligne->nom_ligne)), ['ensemble', 'total', 'total général', 'totaux']);
        };

        $lignesDonnees = [];
        foreach ($lignesRacines as $ligne) {
            if ($isLigneIgnoree($ligne)) {
                continue;
            }
            $lignesDonnees[] = ['ligne' => $ligne, 'is_sous_ligne' => false];
            foreach ($ligne->enfants as $sousLigne) {
                $lignesDonnees[] = ['ligne' => $sousLigne, 'is_sous_ligne' => true];
            }
        }

        $totalColonnes = count($colonnesFeuilles) + 1; // +1 pour la colonne d'intitulé des lignes

        // En-têtes institutionnels
        $sheet->setCellValue([1, 1], 'MINISTÈRE DES INFRASTRUCTURES ET DU DÉSENCLAVEMENT');
        $sheet->getStyle([1, 1])->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1F4E78'));

        $sheet->setCellValue([1, 2], 'DIRECTION GÉNÉRALE DES ÉTUDES ET DES STATISTIQUES SECTORIELLES (DGESS)');
        $sheet->getStyle([1, 2])->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('595959'));

        $sheet->setCellValue([1, 3], 'CANEVAS DE COLLECTE STATISTIQUE — VERSION : ' . ($this->assignation->version->code ?? '') . ' (' . ($this->assignation->version->annee ?? '') . ')');
        $sheet->getStyle([1, 3])->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1F4E78'));

        $sheet->setCellValue([1, 4], 'TABLEAU : ' . $this->tableau->libelle);
        $sheet->getStyle([1, 4])->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue([1, 5], 'STRUCTURE : ' . ($this->assignation->structure->nom ?? '') . ' [' . ($this->assignation->structure->sigle ?? '') . ']');
        $sheet->getStyle([1, 5])->getFont()->setItalic(true)->setSize(10);

        $sheet->setCellValue([1, 6], 'Consigne : Veuillez renseigner les valeurs numériques dans les cellules du tableau ci-dessous. Ne pas modifier les en-têtes.');
        $sheet->getStyle([1, 6])->getFont()->setItalic(true)->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7F7F7F'));

        // Début de la grille matricielle à la ligne 8
        $headerRowStart = 8;
        $headerRowSub = 9;
        $dataRowStart = 10;

        $hasSubColumns = $colonnesRacines->contains(fn($c) => $c->enfants->count() > 0);
        if (!$hasSubColumns) {
            $headerRowSub = $headerRowStart;
            $dataRowStart = 9;
        }

        // Colonne 1 : Intitulé des lignes
        $sheet->setCellValue([1, $headerRowStart], $libelleLigneHeader);
        if ($hasSubColumns) {
            $sheet->mergeCells([1, $headerRowStart, 1, $headerRowSub]);
        }
        $sheet->getStyle([1, $headerRowStart])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // En-têtes de colonnes
        $colIdx = 2;
        foreach ($colonnesRacines as $col) {
            $nbEnfants = $col->enfants->count();
            if ($nbEnfants > 0) {
                // Colonne parente fusionnée sur ses enfants
                $endColIdx = $colIdx + $nbEnfants - 1;
                $sheet->setCellValue([$colIdx, $headerRowStart], $col->colonne_ligne);
                $sheet->mergeCells([$colIdx, $headerRowStart, $endColIdx, $headerRowStart]);
                $sheet->getStyle([$colIdx, $headerRowStart])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach ($col->enfants as $sousCol) {
                    $sheet->setCellValue([$colIdx, $headerRowSub], $sousCol->colonne_ligne);
                    $sheet->getStyle([$colIdx, $headerRowSub])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $colIdx++;
                }
            } else {
                $sheet->setCellValue([$colIdx, $headerRowStart], $col->colonne_ligne);
                if ($hasSubColumns) {
                    $sheet->mergeCells([$colIdx, $headerRowStart, $colIdx, $headerRowSub]);
                }
                $sheet->getStyle([$colIdx, $headerRowStart])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }
        }

        // Style des en-têtes
        $maxColLetter = Coordinate::stringFromColumnIndex($totalColonnes);
        $headerRange = "A{$headerRowStart}:{$maxColLetter}" . ($hasSubColumns ? $headerRowSub : $headerRowStart);
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ]);

        // 2. Préparation de la feuille masquée de métadonnées de mapping
        $sheetMeta = $spreadsheet->createSheet();
        $sheetMeta->setTitle('_METADATA');
        $sheetMeta->setShowGridLines(true);
        $sheetMeta->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $metaHeaders = [
            'Coord_Excel',
            'Cellule_ID',
            'Ligne_ID',
            'Ligne_Nom',
            'Colonne_ID',
            'Colonne_Nom',
            'Tableau_ID',
            'Assignation_ID',
            'Structure_ID'
        ];
        foreach ($metaHeaders as $idx => $metaH) {
            $sheetMeta->setCellValue([$idx + 1, 1], $metaH);
        }
        $sheetMeta->getStyle('A1:I1')->getFont()->setBold(true);

        // Métadonnées globales d'identification et checksum
        $sheetMeta->setCellValue('K1', 'TABLEAU_ID');
        $sheetMeta->setCellValue('L1', $this->tableau->id);
        $sheetMeta->setCellValue('K2', 'ASSIGNATION_ID');
        $sheetMeta->setCellValue('L2', $this->assignation->id);
        $sheetMeta->setCellValue('K3', 'STRUCTURE_ID');
        $sheetMeta->setCellValue('L3', $this->assignation->structure_id);
        $sheetMeta->setCellValue('K4', 'VERSION_ID');
        $sheetMeta->setCellValue('L4', $this->assignation->version_id);

        $currentRow = $dataRowStart;
        $metaRow = 2;
        $celluleIdsSequence = [];

        // Remplissage des lignes de données
        foreach ($lignesDonnees as $item) {
            $ligneObj = $item['ligne'];
            $isSousLigne = $item['is_sous_ligne'];

            $ligneNomAffiche = ($isSousLigne ? '    ' : '') . $ligneObj->nom_ligne;
            $sheet->setCellValue([1, $currentRow], $ligneNomAffiche);
            
            if ($isSousLigne) {
                $sheet->getStyle([1, $currentRow])->getFont()->setItalic(true);
                $sheet->getStyle("A{$currentRow}:{$maxColLetter}{$currentRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            } else {
                $sheet->getStyle([1, $currentRow])->getFont()->setBold(true);
            }

            // Parcours des colonnes pour les cellules de données
            $curCol = 2;
            foreach ($colonnesFeuilles as $colLeaf) {
                // Assurer l'existence de la cellule
                $cellule = Cellule::firstOrCreate(
                    [
                        'ligne_id' => $ligneObj->id,
                        'colonne_id' => $colLeaf->id,
                    ],
                    [
                        'id' => (string) Str::ulid(),
                    ]
                );

                $cellCoord = Coordinate::stringFromColumnIndex($curCol) . $currentRow;
                $celluleIdsSequence[] = $cellule->id;

                // Pré-remplir avec la soumission existante si présente
                $valeurInitiale = $this->soumissions[$cellule->id] ?? null;
                if ($valeurInitiale !== null && $valeurInitiale !== '') {
                    $sheet->setCellValue([$curCol, $currentRow], (float) $valeurInitiale);
                }

                $sheet->getStyle([$curCol, $currentRow])
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $sheet->getStyle([$curCol, $currentRow])
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Enregistrer dans _METADATA
                $sheetMeta->setCellValue([1, $metaRow], $cellCoord);
                $sheetMeta->setCellValue([2, $metaRow], $cellule->id);
                $sheetMeta->setCellValue([3, $metaRow], $ligneObj->id);
                $sheetMeta->setCellValue([4, $metaRow], $ligneObj->nom_ligne);
                $sheetMeta->setCellValue([5, $metaRow], $colLeaf->id);
                $sheetMeta->setCellValue([6, $metaRow], $colLeaf->colonne_ligne);
                $sheetMeta->setCellValue([7, $metaRow], $this->tableau->id);
                $sheetMeta->setCellValue([8, $metaRow], $this->assignation->id);
                $sheetMeta->setCellValue([9, $metaRow], $this->assignation->structure_id);

                $metaRow++;
                $curCol++;
            }

            $currentRow++;
        }

        // Checksum d'intégrité de la structure matricielle
        $checksum = hash('sha256', implode('|', $celluleIdsSequence) . '|' . $this->tableau->id . '|' . $this->assignation->id);
        $sheetMeta->setCellValue('K5', 'CHECKSUM');
        $sheetMeta->setCellValue('L5', $checksum);

        // Bordures de la grille de données
        $lastDataRow = max($currentRow - 1, $dataRowStart);
        $dataGridRange = "A{$dataRowStart}:{$maxColLetter}{$lastDataRow}";
        $sheet->getStyle($dataGridRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9']
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1F4E78']
                ]
            ]
        ]);

        // Ajustement automatique des largeurs de colonnes
        $sheet->getColumnDimension('A')->setWidth(35);
        for ($c = 2; $c <= $totalColonnes; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(18);
        }

        // Revenir sur la première feuille comme feuille active
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
}
