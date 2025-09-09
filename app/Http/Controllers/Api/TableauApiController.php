<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tableau;
use App\Models\SoumissionTableau;
use App\Services\TableauCalculService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TableauApiController extends Controller
{
    protected $calculService;

    public function __construct(TableauCalculService $calculService)
    {
        $this->calculService = $calculService;
    }

    public function index(): JsonResponse
    {
        $tableaux = Tableau::with(['categorie', 'lignesTableau', 'colonnesTableau'])
                          ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tableaux
        ]);
    }

    public function show($id): JsonResponse
    {
        $tableau = Tableau::with([
            'categorie',
            'lignesTableau' => function($query) {
                $query->orderBy('ordre');
            },
            'colonnesTableau' => function($query) {
                $query->orderBy('ordre');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tableau
        ]);
    }

    public function getData($id, Request $request): JsonResponse
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'assignation_id' => 'required|exists:assignations,id'
        ]);

        $soumissions = SoumissionTableau::where('tableau_id', $id)
                                      ->where('structure_id', $request->structure_id)
                                      ->where('assignation_id', $request->assignation_id)
                                      ->with(['cellule.ligne', 'cellule.colonne'])
                                      ->get();

        $data = [];
        foreach ($soumissions as $soumission) {
            $key = $soumission->cellule->ligne->nom_ligne . '_' . $soumission->cellule->colonne->colonne_ligne;
            $data[$key] = $soumission->valeur;
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function saveData($id, Request $request): JsonResponse
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'assignation_id' => 'required|exists:assignations,id',
            'donnees' => 'required|array'
        ]);

        try {
            \DB::beginTransaction();

            foreach ($request->donnees as $cellule_id => $valeur) {
                if (!empty($valeur)) {
                    SoumissionTableau::updateOrCreate(
                        [
                            'tableau_id' => $id,
                            'structure_id' => $request->structure_id,
                            'assignation_id' => $request->assignation_id,
                            'cellule_id' => $cellule_id
                        ],
                        [
                            'valeur' => $valeur
                        ]
                    );
                }
            }

            // Appliquer les règles de gestion
            $this->calculService->appliquerReglesGestion($id, $request->assignation_id);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Données sauvegardées avec succès'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCalculs($id, Request $request): JsonResponse
    {
        $request->validate([
            'assignation_id' => 'required|exists:assignations,id'
        ]);

        $tableau = Tableau::findOrFail($id);
        
        $totauxLignes = [];
        $totauxColonnes = [];

        // Calculer totaux par ligne
        foreach ($tableau->lignesTableau as $ligne) {
            $totauxLignes[$ligne->id] = $this->calculService->calculerTotauxLigne(
                $ligne->id, 
                $request->assignation_id
            );
        }

        // Calculer totaux par colonne
        foreach ($tableau->colonnesTableau as $colonne) {
            $totauxColonnes[$colonne->id] = $this->calculService->calculerTotauxColonne(
                $colonne->id, 
                $request->assignation_id
            );
        }

        $totalGeneral = $this->calculService->calculerTotalGeneral($id, $request->assignation_id);

        return response()->json([
            'success' => true,
            'data' => [
                'totaux_lignes' => $totauxLignes,
                'totaux_colonnes' => $totauxColonnes,
                'total_general' => $totalGeneral
            ]
        ]);
    }

    public function exportExcel($id, Request $request)
    {
        // Implémentation de l'export Excel
        $tableau = Tableau::with(['lignesTableau', 'colonnesTableau'])->findOrFail($id);
        
        // Utiliser une librairie comme PhpSpreadsheet ou Laravel Excel
        return response()->json([
            'success' => true,
            'message' => 'Export en cours de développement'
        ]);
    }
}