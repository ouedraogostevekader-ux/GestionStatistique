<?php

namespace App\Http\Controllers;

use App\Exports\AnnuaireExport;
use App\Models\Tableau;
use App\Models\Version;
use App\Services\AnnuaireService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

class AnnuaireController extends Controller implements HasMiddleware
{
    protected AnnuaireService $annuaireService;

    public function __construct(AnnuaireService $annuaireService)
    {
        $this->annuaireService = $annuaireService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:annuaire-list', only: ['index', 'show']),
            new Middleware('permission:annuaire-create', only: ['create', 'store']),
            new Middleware('permission:annuaire-edit', only: ['edit', 'update']),
            new Middleware('permission:annuaire-delete', only: ['destroy']),

            new Middleware('permission:exportWord|annuaire-list', only: ['exportWord']),
            new Middleware('permission:exportPdf|annuaire-list', only: ['exportPdf']),
            new Middleware('permission:exportExcel|annuaire-list', only: ['exportExcel']),
        ];
    }

    public function index(Request $request)
    {
        $version = $request->input('version');
        $categorie = $request->input('categorie');
        $tableau = $request->input('tableau');

        $donnees = $this->annuaireService->getDonneesAnnuaire($version, $categorie, $tableau);

        return view('annuaires.index', [
            'versions' => $donnees['versions'],
            'version' => $donnees['version']?->id ?? $version,
            'versionActive' => $donnees['version'],
            'categories' => $donnees['categories'] ?? collect(),
            'categorieActive' => $categorie,
            'tableauActive' => $tableau,
            'tableaux' => $donnees['tableaux'],
            'matrice' => $donnees['matrice'],
            'totauxLignes' => $donnees['totauxLignes'],
            'totauxColonnes' => $donnees['totauxColonnes'],
            'totalGeneral' => $donnees['totalGeneral'],
            'assignations' => $donnees['assignations'],
            'consolidation' => $donnees['version']?->tauxValidation() ?? null,
        ]);
    }

    public function exportPDF(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $versionId = $request->input('version');
        $categorieId = $request->input('categorie');
        $tableauId = $request->input('tableau');

        $donnees = $this->annuaireService->getDonneesAnnuaire($versionId, $categorieId, $tableauId);
        $version = $donnees['version'];

        if (!$version) {
            return redirect()->route('annuaires.index')->with('error', 'Veuillez sélectionner une version valide.');
        }

        $pdf = Pdf::loadView('annuaires.export', [
            'version' => $version,
            'tableaux' => $donnees['tableaux'],
            'matrice' => $donnees['matrice'],
            'totauxLignes' => $donnees['totauxLignes'],
            'totauxColonnes' => $donnees['totauxColonnes'],
            'totalGeneral' => $donnees['totalGeneral'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Annuaire-' . ($version->annee ?? 'statistique') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $versionId = $request->input('version');
        $categorieId = $request->input('categorie');
        $tableauId = $request->input('tableau');

        $donnees = $this->annuaireService->getDonneesAnnuaire($versionId, $categorieId, $tableauId);
        $version = $donnees['version'];

        if (!$version) {
            return redirect()->route('annuaires.index')->with('error', 'Veuillez sélectionner une version valide.');
        }

        $filename = 'Annuaire-' . ($version->annee ?? 'statistique') . '.xlsx';

        return (new AnnuaireExport($donnees))->download($filename);
    }

    public function exportWord(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $versionId = $request->input('version');
        $categorieId = $request->input('categorie');
        $tableauId = $request->input('tableau');

        $donnees = $this->annuaireService->getDonneesAnnuaire($versionId, $categorieId, $tableauId);
        $version = $donnees['version'];

        if (!$version) {
            return redirect()->route('annuaires.index')->with('error', 'Veuillez sélectionner une version valide.');
        }

        $tableaux = $donnees['tableaux'];
        $matrice = $donnees['matrice'];
        $totauxLignes = $donnees['totauxLignes'];
        $totauxColonnes = $donnees['totauxColonnes'];
        $totalGeneral = $donnees['totalGeneral'];

        $phpWord = new PhpWord();

        // Style de document et marges
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 720,
            'marginBottom' => 720,
            'marginLeft' => 720,
            'marginRight' => 720,
        ]);

        // Titre officiel
        $section->addTitle("ANNUAIRE STATISTIQUE - ÉDITION " . ($version->annee ?? ''), 1);
        $section->addText(
            "Ministère des Infrastructures - Direction Générale des Études et des Statistiques Sectorielles (DGESS)",
            ['italic' => true, 'color' => '555555', 'size' => 10]
        );
        $section->addTextBreak(1);

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER,
        ];
        $phpWord->addTableStyle('AnnuaireTable', $tableStyle);

        $headerCellStyle = ['bgColor' => 'E2E8F0'];
        $headerParentStyle = ['bgColor' => 'CBD5E1'];
        $ligneParentStyle = ['bgColor' => 'F1F5F9'];
        $totalCellStyle = ['bgColor' => 'CBD5E1'];

        $fontBold = ['bold' => true, 'size' => 9];
        $fontRegular = ['size' => 9];
        $fontTotal = ['bold' => true, 'color' => '003366', 'size' => 9];

        $isTotalOrEnsemble = fn($nom) => in_array(mb_strtolower(trim($nom)), ['ensemble', 'total', 'total général', 'totaux']);

        foreach ($tableaux as $tableau) {
            if ($tableau->categorie) {
                $section->addText(
                    "Catégorie : " . mb_strtoupper($tableau->categorie->libelle),
                    ['bold' => true, 'color' => '718096', 'size' => 9]
                );
            }
            $section->addText($tableau->libelle, ['bold' => true, 'size' => 11, 'color' => '003366']);

            $table = $section->addTable('AnnuaireTable');

            $lignesRacines = $tableau->lignesTableau->whereNull('parent_id')
                ->filter(fn($l) => $l->ordre != 1 && !$isTotalOrEnsemble($l->nom_ligne));
            $colonnesRacines = $tableau->colonnesTableau->whereNull('parent_id');
            $headerLigne = $tableau->lignesTableau->firstWhere('ordre', 1);
            $headerTitre = $headerLigne ? $headerLigne->nom_ligne : 'Libellé';
            $hasSousColonnes = $colonnesRacines->contains(fn($c) => $c->enfants && $c->enfants->count() > 0);

            // En-tête ligne 1
            $table->addRow();
            $table->addCell(3000, $headerCellStyle)->addText($headerTitre, $fontBold);

            foreach ($colonnesRacines as $col) {
                if ($col->enfants && $col->enfants->count() > 0) {
                    $table->addCell(2000 * $col->enfants->count(), ['gridSpan' => $col->enfants->count(), 'bgColor' => 'E2E8F0'])
                        ->addText($col->colonne_ligne, $fontBold, ['alignment' => Jc::CENTER]);
                } else {
                    $table->addCell(2000, $headerCellStyle)
                        ->addText($col->colonne_ligne, $fontBold, ['alignment' => Jc::CENTER]);
                }
            }
            $table->addCell(2000, $headerParentStyle)->addText("Total", $fontBold, ['alignment' => Jc::CENTER]);

            // En-tête ligne 2 (sous-colonnes) si présentes
            if ($hasSousColonnes) {
                $table->addRow();
                $table->addCell(3000, $headerCellStyle)->addText("", $fontRegular);
                foreach ($colonnesRacines as $col) {
                    if ($col->enfants && $col->enfants->count() > 0) {
                        foreach ($col->enfants as $sousCol) {
                            $table->addCell(2000, $headerCellStyle)
                                ->addText($sousCol->colonne_ligne, $fontBold, ['alignment' => Jc::CENTER]);
                        }
                    } else {
                        $table->addCell(2000, $headerCellStyle)->addText("", $fontRegular);
                    }
                }
                $table->addCell(2000, $headerParentStyle)->addText("", $fontRegular);
            }

            // Corps du tableau
            foreach ($lignesRacines as $ligne) {
                $hasSousLignes = $ligne->enfants && $ligne->enfants->count() > 0;

                if ($hasSousLignes) {
                    // Ligne Parente
                    $table->addRow();
                    $table->addCell(3000, $ligneParentStyle)->addText($ligne->nom_ligne, $fontBold);

                    foreach ($colonnesRacines as $col) {
                        if ($col->enfants && $col->enfants->count() > 0) {
                            foreach ($col->enfants as $sousCol) {
                                $val = $matrice[$tableau->id][$ligne->id][$sousCol->id] ?? '-';
                                $table->addCell(2000, $ligneParentStyle)
                                    ->addText((string)$val, $fontBold, ['alignment' => Jc::RIGHT]);
                            }
                        } else {
                            $val = $matrice[$tableau->id][$ligne->id][$col->id] ?? '-';
                            $table->addCell(2000, $ligneParentStyle)
                                ->addText((string)$val, $fontBold, ['alignment' => Jc::RIGHT]);
                        }
                    }
                    $totLigne = $totauxLignes[$tableau->id][$ligne->id] ?? '-';
                    $table->addCell(2000, $totalCellStyle)
                        ->addText((string)$totLigne, $fontBold, ['alignment' => Jc::RIGHT]);

                    // Sous-lignes
                    foreach ($ligne->enfants as $sousLigne) {
                        $table->addRow();
                        $table->addCell(3000)->addText("  • " . $sousLigne->nom_ligne, $fontRegular);

                        foreach ($colonnesRacines as $col) {
                            if ($col->enfants && $col->enfants->count() > 0) {
                                foreach ($col->enfants as $sousCol) {
                                    $val = $matrice[$tableau->id][$sousLigne->id][$sousCol->id] ?? '-';
                                    $table->addCell(2000)->addText((string)$val, $fontRegular, ['alignment' => Jc::RIGHT]);
                                }
                            } else {
                                $val = $matrice[$tableau->id][$sousLigne->id][$col->id] ?? '-';
                                $table->addCell(2000)->addText((string)$val, $fontRegular, ['alignment' => Jc::RIGHT]);
                            }
                        }
                        $totSousLigne = $totauxLignes[$tableau->id][$sousLigne->id] ?? '-';
                        $table->addCell(2000, ['bgColor' => 'F8FAFC'])
                            ->addText((string)$totSousLigne, $fontBold, ['alignment' => Jc::RIGHT]);
                    }
                } else {
                    // Ligne simple
                    $table->addRow();
                    $table->addCell(3000)->addText($ligne->nom_ligne, $fontRegular);

                    foreach ($colonnesRacines as $col) {
                        if ($col->enfants && $col->enfants->count() > 0) {
                            foreach ($col->enfants as $sousCol) {
                                $val = $matrice[$tableau->id][$ligne->id][$sousCol->id] ?? '-';
                                $table->addCell(2000)->addText((string)$val, $fontRegular, ['alignment' => Jc::RIGHT]);
                            }
                        } else {
                            $val = $matrice[$tableau->id][$ligne->id][$col->id] ?? '-';
                            $table->addCell(2000)->addText((string)$val, $fontRegular, ['alignment' => Jc::RIGHT]);
                        }
                    }
                    $totLigne = $totauxLignes[$tableau->id][$ligne->id] ?? '-';
                    $table->addCell(2000, ['bgColor' => 'F8FAFC'])
                        ->addText((string)$totLigne, $fontBold, ['alignment' => Jc::RIGHT]);
                }
            }

            // Ligne Total Général
            $table->addRow();
            $table->addCell(3000, $totalCellStyle)->addText("TOTAL GÉNÉRAL", $fontBold);

            foreach ($colonnesRacines as $col) {
                if ($col->enfants && $col->enfants->count() > 0) {
                    foreach ($col->enfants as $sousCol) {
                        $totCol = $totauxColonnes[$tableau->id][$sousCol->id] ?? '-';
                        $table->addCell(2000, $totalCellStyle)
                            ->addText((string)$totCol, $fontBold, ['alignment' => Jc::RIGHT]);
                    }
                } else {
                    $totCol = $totauxColonnes[$tableau->id][$col->id] ?? '-';
                    $table->addCell(2000, $totalCellStyle)
                        ->addText((string)$totCol, $fontBold, ['alignment' => Jc::RIGHT]);
                }
            }
            $totGen = $totalGeneral[$tableau->id] ?? '-';
            $table->addCell(2000, ['bgColor' => '94A3B8'])
                ->addText((string)$totGen, ['bold' => true, 'color' => 'FFFFFF', 'size' => 9], ['alignment' => Jc::RIGHT]);

            $section->addTextBreak(2);
        }

        $filename = "Annuaire-" . ($version->annee ?? 'statistique') . ".docx";
        $tempPath = storage_path('app/' . $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend();
    }
}
