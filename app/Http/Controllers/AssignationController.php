<?php

namespace App\Http\Controllers;

use App\Models\Assignation;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Tableau;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;

use function Symfony\Component\Clock\now;

class AssignationController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:assignation-list', only: ['index', 'show', 'dashboard']),
            new Middleware('permission:assignation-create', only: ['create', 'store', 'affectationM', 'storeM']),
            new Middleware('permission:assignation-edit', only: ['edit', 'update']),
            new Middleware('permission:assignation-delete', only: ['destroy']),
        ];
    }


    public function index()
    {
        $assignations = Assignation::with(['tableau', 'structure'])->paginate(10);
        $structures = Structure::orderBy('nom')->get();
        return view('tableaux.assignations.assignations-dashboard', compact('assignations', 'structures'));
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

        return view('tableaux.assignations.assignations-dashboard', compact('tableau', 'assignations', 'structures'));
    }

    /**
     * Dashboard des assignations
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Base query selon les permissions
        $baseQuery = Assignation::with(['tableau.categorie', 'structure', 'version']);

        if (!$user->isAdmin()) {
            $baseQuery->where('structure_id', $user->structure_id);
        }

        // Filtres
        if ($request->filled('statut')) {
            $baseQuery->where('statut', $request->statut);
        }

        if ($request->filled('version_id')) {
            $baseQuery->where('version_id', $request->version_id);
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

        $versions = Version::all();

        // Assignations récentes
        $assignations = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->where('statut', '<', 3)
            //->limit(10)
            ->get();

        // Assignations en retard (exemple - à adapter selon vos besoins)
        $assignationsEnRetard = (clone $baseQuery)
            ->where('statut', 3) // Pas terminées
            ->whereNotNull('date_fin')
            //->where('date_fin', '<', Carbon::now()->toDateString())
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

        return view('tableaux.assignations.assignations-dashboard', compact(
            'stats',
            'assignations',
            'assignationsEnRetard',
            'structures',
            'versions',
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

        return view('tableaux.assignations.assignationM', compact('tableau', 'structures', 'periodes', 'statuts'));
    }

    /**
     * Enregistrer une nouvelle assignation
     */
    public function store(Request $request, $tableauId)
    {
        $request->validate([
            'structure_ids' => 'required|array|min:1',
            'structure_ids.*' => 'exists:structures,id',
            'version_id' => [
                'required',
                Rule::unique('assignations')->where(function ($q) use ($request) {
                    return $q->where('structure_id', $request->structure_id)
                        ->where('version_id', $request->version_id)
                        ->where('tableau_id', $request->tableau_id);
                })
            ],
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
            'version_id.unique' => 'Ce tableau ne peut pas être assigné plusieurs fois à la même structure avec la même version.',
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
                        'annee' => Carbon::now()->year,
                        'statut' => Assignation::determinerStatut($request->date_debut, $request->date_fin),
                        // 'periode' => $request->periode,
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
    public function show($id)
    {
        $assignation = Assignation::with(['structure', 'tableau', 'version'])->findOrFail($id);
        $soumissions = SoumissionTableau::where('assignation_id', $assignation->id)->get();
        //dd($soumissions);
        return view('tableaux.assignations.assignation-show', compact('assignation', 'soumissions'));
    }

    public function edit($id)
    {
        $assignation = Assignation::findOrFail($id);
        $structures = Structure::all();
        $tableaux = Tableau::all();
        $versions = Version::all();

        return view('tableaux.assignations.assignation-edit', compact('assignation', 'structures', 'tableaux', 'versions'));
    }

    public function update(Request $request, $id)
    {
        $assignation = Assignation::findOrFail($id);

        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'tableau_id' => 'required|exists:tableaux,id',
            'version_id' => 'nullable|exists:versions,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $assignation->update([
            'structure_id' => $request->structure_id,
            'tableau_id' => $request->tableau_id,
            'version_id' => $request->version_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => Assignation::determinerStatut($request->date_debut, $request->date_fin),
        ]);

        return redirect()->route('assignations.dashboard')
            ->with('success', 'Assignation mise à jour avec succès.');
    }


    public function affectionM()
    {
        $tableaux = Tableau::all();
        $structures = Structure::all();
        $versions = Version::all();
        return view('tableaux.assignations.assignationM', compact('tableaux', 'structures', 'versions'));
    }

    public function storeM(Request $request)
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'tableau_ids' => 'required|array|min:1',
            'tableau_ids.*' => 'exists:tableaux,id',
            'version_id' => 'nullable|string|max:250',
            // 'source' => 'nullable|string|max:250',
            // 'statut' => 'required|integer|in:0,1,2,3',
            'responsable' => 'nullable|string|max:250',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'annee' => 'nullable|integer|min:2000|max:2100',
            'periode' => 'nullable|string|max:250',
        ], [
            'structure_id.required' => 'Vous devez sélectionner au moins une structure.',
            'structure_id.array' => 'Le format des structures sélectionnées n\'est pas valide.',
            'structure_ids.*.exists' => 'Une des structures sélectionnées n\'existe pas.',
            // 'statut.required' => 'Le statut est obligatoire.',
            // 'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année ne peut pas être antérieure à 2000.',
            'annee.max' => 'L\'année ne peut pas être supérieure à 2100.',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->tableau_ids as $tableauId) {

                $exists = Assignation::where('tableau_id', $tableauId)
                    ->where('structure_id', $request->structure_id)
                    ->first();

                if (!$exists) {
                    Assignation::create([
                        'tableau_id' => $tableauId,
                        'structure_id' => $request->structure_id,
                        'version_id' => $request->version_id,
                        // 'source' => $request->source,
                        // 'statut' => $request->statut,
                        // 'responsable' => $request->responsable,
                        'date_debut' => $request->date_debut,
                        'date_fin' => $request->date_fin,
                        'annee' => Carbon::now()->year,
                        'statut' => Assignation::determinerStatut($request->date_debut, $request->date_fin),
                        // 'periode' => $request->periode,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('assignations.dashboard')->with(
                'success',
                count($request->tableau_ids) . ' tableau(x) assigné(s) à la structure avec succès.'
            );
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l’assignation : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une assignation
     */
    public function destroy($assignationId)
    {
        try {
            $assignation = Assignation::findOrFail($assignationId);
            $assignation->delete();

            return redirect()->route('assignations.dashboard')
                ->with('success', "L'assignation a été supprimée avec succès.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'une assignation
     */
    // public function changeStatus(Request $request, $tableauId, $assignationId)
    // {
    //     $request->validate([
    //         'statut' => 'required|integer|in:0,1,2,3'
    //     ]);

    //     try {
    //         $assignation = Assignation::findOrFail($assignationId);

    //         // Vérifier que l'assignation appartient bien au tableau
    //         if ($assignation->tableau_id != $tableauId) {
    //             abort(404);
    //         }

    //         $assignation->update(['statut' => $request->statut]);

    //         $statutLabels = [
    //             0 => 'En attente',
    //             1 => 'En cours',
    //             2 => 'Terminé',
    //             3 => 'Suspendu'
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Le statut a été changé à '{$statutLabels[$request->statut]}'"
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Une erreur est survenue : ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}
