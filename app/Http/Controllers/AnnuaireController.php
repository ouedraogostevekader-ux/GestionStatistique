<?php

namespace App\Http\Controllers;

use App\Models\Assignation;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\SoumissionTableau;
use App\Models\Tableau;
use App\Models\Version;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\AnnuaireService;

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

            new Middleware('permission:exportWord', only: ['exportWord']),
            new Middleware('permission:exportPdf', only: ['exportPdf']),
        ];
    }

    public function index(Request $request)
    {
        $version = $request->input('version');
        $donnees = $this->annuaireService->getDonneesAnnuaire($version);

        return view('annuaires.index', [
            'versions' => $donnees['versions'],
            'version' => $donnees['version']?->id ?? $version,
            'versionActive' => $donnees['version'],
            'tableaux' => $donnees['tableaux'],
            'matrice' => $donnees['matrice'],
            'totauxLignes' => $donnees['totauxLignes'],
            'totauxColonnes' => $donnees['totauxColonnes'],
            'totalGeneral' => $donnees['totalGeneral'],
            'assignations' => $donnees['assignations'],
        ]);
    }

    private function getTableauxIds($versionId)
    {
        return Assignation::where('version_id', $versionId)
            //->where('structure_id', auth()->user()->structure_id)
            ->pluck('tableau_id');
    }

    public function exportPDF(Request $request)
    {
        $version = Version::find($request->version);
        $tableaux = Tableau::whereIn('id', $this->getTableauxIds($request->version))->get();
        //dd($tableaux);
        $pdf = Pdf::loadView('annuaires.export', compact('tableaux', 'version'));
        return $pdf->download('Annuaire-' . $version->annee . '.pdf');
    }

    public function exportWord(Request $request)
    {
        $version = Version::find($request->version);
        $tableaux = Tableau::whereIn('id', $this->getTableauxIds($request->version))->get();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $section->addTitle("Annuaire Statistique - " . $version->annee, 1);

        foreach ($tableaux as $tableau) {
            $section->addText($tableau->libelle, ['bold' => true]);

            $table = $section->addTable();

            $lignes = $tableau->lignesTableau;
            $colonnes = $tableau->colonnesTableau;

            // Header
            $table->addRow();
            $table->addCell(3000)->addText("Lignes");
            foreach ($colonnes as $col) {
                $table->addCell(2000)->addText($col->colonne_ligne);
            }

            // Data
            foreach ($lignes as $ligne) {
                $table->addRow();
                $table->addCell(3000)->addText($ligne->nom_ligne);

                foreach ($colonnes as $col) {
                    $cellule = Cellule::where('ligne_id', $ligne->id)
                        ->where('colonne_id', $col->id)
                        ->first();

                    $valeur = $cellule
                        ? SoumissionTableau::where('cellule_id', $cellule->id)->value('valeur') ?? '-'
                        : '-';

                    $table->addCell(2000)->addText($valeur);
                }
            }

            $section->addTextBreak();
        }

        $filename = "Annuaire-" . $version->annee . ".docx";

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend();
    }
}
