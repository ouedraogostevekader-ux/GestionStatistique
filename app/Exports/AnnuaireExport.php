<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AnnuaireExport
{
    protected array $donnees;

    public function __construct(array $donnees)
    {
        $this->donnees = $donnees;
    }

    /**
     * Construit et télécharge le fichier Excel à haute performance sans passer par le parseur HTML.
     */
    public function download(string $filename): BinaryFileResponse
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'annuaire_excel_') . '.xlsx';
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
     * Génère l'objet Spreadsheet directement en mémoire.
     */
    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('DGESS - Ministère des Infrastructures')
            ->setTitle('Annuaire Statistique - Édition ' . ($this->donnees['version']->annee ?? ''));

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Annuaire Statistique');
        $sheet->setShowGridLines(true);

        $isTotalOrEnsemble = fn($nom) => in_array(mb_strtolower(trim($nom)), ['ensemble', 'total', 'total général', 'totaux']);

        $currentRow = 1;

        // En-tête officiel du document
        $sheet->setCellValue([1, $currentRow], 'ANNUAIRE STATISTIQUE - ÉDITION ' . ($this->donnees['version']->annee ?? ''));
        $sheet->getStyle([1, $currentRow])->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('003366');
        $sheet->getStyle([1, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;

        $sheet->setCellValue([1, $currentRow], 'Ministère des Infrastructures - Direction Générale des Études et des Statistiques Sectorielles (DGESS)');
        $sheet->getStyle([1, $currentRow])->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('64748B');
        $sheet->getStyle([1, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow += 2;

        $maxColUsed = 1;

        foreach ($this->donnees['tableaux'] as $tableau) {
            // 1. Catégorie
            if ($tableau->categorie) {
                $sheet->setCellValue([1, $currentRow], 'CATÉGORIE : ' . mb_strtoupper($tableau->categorie->libelle));
                $sheet->getStyle([1, $currentRow])->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('475569');
                $currentRow++;
            }

            $colonnesRacines = $tableau->colonnesTableau->whereNull('parent_id');
            $headerLigne = $tableau->lignesTableau->firstWhere('ordre', 1);
            $headerTitre = $headerLigne ? $headerLigne->nom_ligne : 'Libellé';
            $hasSousColonnes = $colonnesRacines->contains(fn($c) => $c->enfants && $c->enfants->count() > 0);

            // Construction de la structure aplatie des colonnes
            $flatColonnes = [];
            $headerRow1 = [$headerTitre];
            $headerRow2 = [''];
            $colSpanMap = [];

            $colIdx = 2;
            foreach ($colonnesRacines as $colonne) {
                $enfants = $colonne->enfants;
                if ($enfants && $enfants->count() > 0) {
                    $span = $enfants->count();
                    $colSpanMap[] = [$colIdx, $span];
                    $headerRow1[] = $colonne->colonne_ligne;
                    for ($i = 1; $i < $span; $i++) {
                        $headerRow1[] = '';
                    }
                    foreach ($enfants as $sousCol) {
                        $flatColonnes[] = $sousCol->id;
                        $headerRow2[] = $sousCol->colonne_ligne;
                    }
                    $colIdx += $span;
                } else {
                    $flatColonnes[] = $colonne->id;
                    $headerRow1[] = $colonne->colonne_ligne;
                    $headerRow2[] = '';
                    $colIdx++;
                }
            }
            $headerRow1[] = 'Total';
            $headerRow2[] = '';

            $totalCols = count($headerRow1);
            if ($totalCols > $maxColUsed) {
                $maxColUsed = $totalCols;
            }

            // 2. Titre du Tableau
            $sheet->setCellValue([1, $currentRow], $tableau->libelle);
            $sheet->mergeCells([1, $currentRow, $totalCols, $currentRow]);
            $sheet->getStyle([1, $currentRow, $totalCols, $currentRow])->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $currentRow++;

            // 3. En-têtes de colonnes
            if ($hasSousColonnes) {
                $sheet->fromArray([$headerRow1, $headerRow2], null, 'A' . $currentRow, true);

                // Fusion verticale pour Libellé
                $sheet->mergeCells([1, $currentRow, 1, $currentRow + 1]);

                // Fusion horizontale pour colonnes parentes
                foreach ($colSpanMap as [$cIdx, $span]) {
                    if ($span > 1) {
                        $sheet->mergeCells([$cIdx, $currentRow, $cIdx + $span - 1, $currentRow]);
                    }
                }

                // Fusion verticale pour Total
                $sheet->mergeCells([$totalCols, $currentRow, $totalCols, $currentRow + 1]);

                $sheet->getStyle([1, $currentRow, $totalCols, $currentRow + 1])->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E293B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
                ]);
                $sheet->getStyle([1, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $currentRow += 2;
            } else {
                $sheet->fromArray([$headerRow1], null, 'A' . $currentRow, true);
                $sheet->getStyle([1, $currentRow, $totalCols, $currentRow])->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E293B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
                ]);
                $sheet->getStyle([1, $currentRow])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $currentRow += 1;
            }

            // 4. Lignes de données
            $lignesRacines = $tableau->lignesTableau->whereNull('parent_id')
                ->filter(fn($l) => $l->ordre != 1 && !$isTotalOrEnsemble($l->nom_ligne));

            $dataStartRow = $currentRow;
            $parentRows = [];

            foreach ($lignesRacines as $ligne) {
                $hasSousLignes = $ligne->enfants && $ligne->enfants->count() > 0;
                $row = [$ligne->nom_ligne];

                foreach ($flatColonnes as $colId) {
                    $val = $this->donnees['matrice'][$tableau->id][$ligne->id][$colId] ?? '-';
                    $row[] = is_numeric($val) ? (float)$val : $val;
                }
                $totL = $this->donnees['totauxLignes'][$tableau->id][$ligne->id] ?? '-';
                $row[] = is_numeric($totL) ? (float)$totL : $totL;

                $sheet->fromArray([$row], null, 'A' . $currentRow, true);

                if ($hasSousLignes) {
                    $parentRows[] = $currentRow;
                    $currentRow++;

                    foreach ($ligne->enfants as $sousLigne) {
                        $subRow = ['   - ' . $sousLigne->nom_ligne];
                        foreach ($flatColonnes as $colId) {
                            $val = $this->donnees['matrice'][$tableau->id][$sousLigne->id][$colId] ?? '-';
                            $subRow[] = is_numeric($val) ? (float)$val : $val;
                        }
                        $subTot = $this->donnees['totauxLignes'][$tableau->id][$sousLigne->id] ?? '-';
                        $subRow[] = is_numeric($subTot) ? (float)$subTot : $subTot;

                        $sheet->fromArray([$subRow], null, 'A' . $currentRow, true);
                        $currentRow++;
                    }
                } else {
                    $currentRow++;
                }
            }

            $dataEndRow = $currentRow - 1;

            // Formatage des lignes de données
            if ($dataEndRow >= $dataStartRow) {
                $sheet->getStyle([1, $dataStartRow, $totalCols, $dataEndRow])->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                ]);
                $sheet->getStyle([2, $dataStartRow, $totalCols, $dataEndRow])->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
                foreach ($parentRows as $pRow) {
                    $sheet->getStyle([1, $pRow, $totalCols, $pRow])->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    ]);
                }
            }

            // 5. Ligne Total Général
            $totalRow = ['TOTAL GÉNÉRAL'];
            foreach ($flatColonnes as $colId) {
                $totCol = $this->donnees['totauxColonnes'][$tableau->id][$colId] ?? '-';
                $totalRow[] = is_numeric($totCol) ? (float)$totCol : $totCol;
            }
            $totGen = $this->donnees['totalGeneral'][$tableau->id] ?? '-';
            $totalRow[] = is_numeric($totGen) ? (float)$totGen : $totGen;

            $sheet->fromArray([$totalRow], null, 'A' . $currentRow, true);
            $sheet->getStyle([1, $currentRow, $totalCols, $currentRow])->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CBD5E1']],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '64748B']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']],
                ],
            ]);
            $sheet->getStyle([2, $currentRow, $totalCols, $currentRow])->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => '#,##0'],
            ]);
            $sheet->getStyle([$totalCols, $currentRow])->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '94A3B8']],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            ]);

            $currentRow += 3;
        }

        // Auto-ajustement des largeurs de colonnes
        $sheet->getColumnDimension('A')->setWidth(38);
        for ($c = 2; $c <= $maxColUsed; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(16);
        }

        return $spreadsheet;
    }
}
