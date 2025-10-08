<?php

namespace App\Http\Controllers;

use App\Models\Assignation;
use App\Models\Structure;
use App\Models\Tableau;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignationController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:assignation-list', only: ['index', 'show']),
            new Middleware('permission:assignation-create', only: ['create', 'store']),
            new Middleware('permission:assignation-edit', only: ['edit', 'update']),
            new Middleware('permission:assignation-delete', only: ['destroy']),
        ];
    }


    public function index()
    {
        $assignations = Assignation::with(['tableau', 'structure'])->paginate(10);
        $structures = Structure::orderBy('nom')->get();
        return view('tableaux.assignations', compact('assignations', 'structures'));
    }

    /**
     * Afficher la liste des assignations pour un tableau
     */
    public function indexByTableau($tableauId)
    {
        $tableau = Tableau::with('categorie')->findOrFail($tableauId);

        $assignations = Assignation::with('structure')
            ->where('tableau_id', $tableauId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $structures = Structure::orderBy('nom')->get();

        return view('tableaux.assignations', compact('tableau', 'assignations', 'structures'));
    }

    /**
     * Dashboard des assignations
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Base query selon les permissions
        $baseQuery = Assignation::with(['tableau.categorie', 'structure']);

        if (!$user->isAdmin()) {
            $baseQuery->where('structure_id', $user->structure_id);
        }

        // Filtres
        if ($request->filled('statut')) {
            $baseQuery->where('statut', $request->statut);
        }

        if ($request->filled('annee')) {
            $baseQuery->where('annee', $request->annee);
        }

        if ($request->filled('structure_id') && $user->isAdmin()) {
            $baseQuery->where('structure_id', $request->structure_id);
        }

        // Statistiques
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'en_attente' => (clone $baseQuery)->where('statut', 0)->count(),
            'en_cours' => (clone $baseQuery)->where('statut', 1)->count(),
            'terminees' => (clone $baseQuery)->where('statut', 2)->count(),
            'suspendues' => (clone $baseQuery)->where('statut', 3)->count(),
        ];

        // Assignations récentes
        $assignations = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Assignations en retard (exemple - à adapter selon vos besoins)
        $assignationsEnRetard = (clone $baseQuery)
            ->where('statut', '!=', 2) // Pas terminées
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', now()->toDateString())
            ->get();

        // Structures (pour les admins)
        $structures = $user->isAdmin() ? Structure::orderBy('nom')->get() : collect();

        // Données mensuelles (exemple simple)
        $monthlyData = [];
        for ($i = 1; $i <= 6; $i++) {
            $monthlyData[] = Assignation::whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->when(!$user->isAdmin(), function ($q) use ($user) {
                    return $q->where('structure_id', $user->structure_id);
                })
                ->count();
        }

        return view('tableaux.assignations-dashboard', compact(
            'stats',
            'assignations',
            'assignationsEnRetard',
            'structures',
            'monthlyData'
        ));
    }

    /**
     * Afficher le formulaire d'assignation
     */
    public function create($tableauId)
    {
        $tableau = Tableau::with('categorie')->findOrFail($tableauId);

        // Récupérer les structures non encore assignées à ce tableau
        $structuresAssignees = Assignation::where('tableau_id', $tableauId)
            ->pluck('structure_id')
            ->toArray();

        $structures = Structure::whereNotIn('id', $structuresAssignees)
            ->orderBy('nom')
            ->get();

        $periodes = [
            'T1' => 'Premier trimestre',
            'T2' => 'Deuxième trimestre',
            'T3' => 'Troisième trimestre',
            'T4' => 'Quatrième trimestre',
            'S1' => 'Premier semestre',
            'S2' => 'Deuxième semestre',
            'ANNUEL' => 'Annuel'
        ];

        $statuts = [
            0 => 'En attente',
            1 => 'En cours',
            2 => 'Terminé',
            3 => 'Suspendu'
        ];

        return view('tableaux.assignation-create', compact('tableau', 'structures', 'periodes', 'statuts'));
    }

    /**
     * Enregistrer une nouvelle assignation
     */
    public function store(Request $request, $tableauId)
    {
        $request->validate([
            'structure_ids' => 'required|array|min:1',
            'structure_ids.*' => 'exists:structures,id',
            'version' => 'nullable|string|max:250',
            'source' => 'nullable|string|max:250',
            'statut' => 'required|integer|in:0,1,2,3',
            'responsable' => 'nullable|string|max:250',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|max:250',
            'annee' => 'nullable|integer|min:2000|max:2100',
            'periode' => 'nullable|string|max:250',
        ], [
            'structure_ids.required' => 'Vous devez sélectionner au moins une structure.',
            'structure_ids.array' => 'Le format des structures sélectionnées n\'est pas valide.',
            'structure_ids.*.exists' => 'Une des structures sélectionnées n\'existe pas.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année ne peut pas être antérieure à 2000.',
            'annee.max' => 'L\'année ne peut pas être supérieure à 2100.',
        ]);
        //  dd($request->all());

        try {
            DB::beginTransaction();

            $tableau = Tableau::findOrFail($tableauId);

            foreach ($request->structure_ids as $structureId) {
                // Vérifier si cette structure n'est pas déjà assignée
                $existingAssignation = Assignation::where('tableau_id', $tableauId)
                    ->where('structure_id', $structureId)
                    ->first();

                if (!$existingAssignation) {
                    Assignation::create([
                        'tableau_id' => $tableauId,
                        'structure_id' => $structureId,
                        'version' => $request->version,
                        'source' => $request->source,
                        'statut' => $request->statut,
                        'responsable' => $request->responsable,
                        'date_debut' => $request->date_debut,
                        'date_fin' => $request->date_fin,
                        'annee' => $request->annee,
                        'periode' => $request->periode,
                    ]);
                }
            }

            DB::commit();

            $structuresCount = count($request->structure_ids);
            $message = $structuresCount > 1
                ? "Le tableau '{$tableau->libelle}' a été assigné à {$structuresCount} structures avec succès."
                : "Le tableau '{$tableau->libelle}' a été assigné à la structure avec succès.";

            return redirect()->route('assignations.dashboard', $tableau)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'assignation : ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de modification d'une assignation
     */
    public function edit($tableauId, $assignationId)
    {
        $tableau = Tableau::with('categorie')->findOrFail($tableauId);
        $assignation = Assignation::with('structure')->findOrFail($assignationId);

        // Vérifier que l'assignation appartient bien au tableau
        if ($assignation->tableau_id != $tableauId) {
            abort(404);
        }

        $structures = Structure::orderBy('nom')->get();

        $periodes = [
            'T1' => 'Premier trimestre',
            'T2' => 'Deuxième trimestre',
            'T3' => 'Troisième trimestre',
            'T4' => 'Quatrième trimestre',
            'S1' => 'Premier semestre',
            'S2' => 'Deuxième semestre',
            'ANNUEL' => 'Annuel'
        ];

        $statuts = [
            0 => 'En attente',
            1 => 'En cours',
            2 => 'Terminé',
            3 => 'Suspendu'
        ];

        return view('assignations.edit', compact('tableau', 'assignation', 'structures', 'periodes', 'statuts'));
    }

    /**
     * Mettre à jour une assignation
     */
    public function update(Request $request, $tableauId, $assignationId)
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'version' => 'nullable|string|max:250',
            'source' => 'nullable|string|max:250',
            'statut' => 'required|integer|in:0,1,2,3',
            'responsable' => 'nullable|string|max:250',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|string|max:250',
            'annee' => 'nullable|integer|min:2000|max:2100',
            'periode' => 'nullable|string|max:250',
        ], [
            'structure_id.required' => 'La structure est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée n\'existe pas.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année ne peut pas être antérieure à 2000.',
            'annee.max' => 'L\'année ne peut pas être supérieure à 2100.',
        ]);

        try {
            $assignation = Assignation::findOrFail($assignationId);

            // Vérifier que l'assignation appartient bien au tableau
            if ($assignation->tableau_id != $tableauId) {
                abort(404);
            }

            // Vérifier si une autre assignation existe déjà pour cette structure (sauf la courante)
            $existingAssignation = Assignation::where('tableau_id', $tableauId)
                ->where('structure_id', $request->structure_id)
                ->where('id', '!=', $assignationId)
                ->first();

            if ($existingAssignation) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cette structure est déjà assignée à ce tableau.');
            }

            $assignation->update([
                'structure_id' => $request->structure_id,
                'version' => $request->version,
                'source' => $request->source,
                'statut' => $request->statut,
                'responsable' => $request->responsable,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'annee' => $request->annee,
                'periode' => $request->periode,
            ]);

            return redirect()->route('assignations.index', $tableauId)
                ->with('success', 'L\'assignation a été mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une assignation
     */
    public function destroy($tableauId, $assignationId)
    {
        try {
            $assignation = Assignation::findOrFail($assignationId);

            // Vérifier que l'assignation appartient bien au tableau
            if ($assignation->tableau_id != $tableauId) {
                abort(404);
            }

            $structureName = $assignation->structure->nom;
            $assignation->delete();

            return redirect()->route('assignations.index', $tableauId)
                ->with('success', "L'assignation de '{$structureName}' a été supprimée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'une assignation
     */
    public function changeStatus(Request $request, $tableauId, $assignationId)
    {
        $request->validate([
            'statut' => 'required|integer|in:0,1,2,3'
        ]);

        try {
            $assignation = Assignation::findOrFail($assignationId);

            // Vérifier que l'assignation appartient bien au tableau
            if ($assignation->tableau_id != $tableauId) {
                abort(404);
            }

            $assignation->update(['statut' => $request->statut]);

            $statutLabels = [
                0 => 'En attente',
                1 => 'En cours',
                2 => 'Terminé',
                3 => 'Suspendu'
            ];

            return response()->json([
                'success' => true,
                'message' => "Le statut a été changé à '{$statutLabels[$request->statut]}'"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }
}
