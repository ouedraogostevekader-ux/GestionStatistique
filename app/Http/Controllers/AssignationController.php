<?php

namespace App\Http\Controllers;

use App\Mail\RelanceAssignationMail;
use App\Mail\RejetAssignationMail;
use App\Mail\ValidationAssignationMail;
use App\Models\Assignation;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Tableau;
use App\Models\User;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            new Middleware('permission:assignation-edit', only: ['edit', 'update', 'valider', 'rejeter', 'relancer', 'relancerRetardataires']),
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

        if ($request->filled('retard') && $request->retard == '1') {
            $baseQuery->where('statut', '!=', 3)
                ->whereNotNull('date_fin')
                ->where('date_fin', '<', Carbon::today()->toDateString());
        }

        // Statistiques
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'en_attente' => (clone $baseQuery)->where('statut', 0)->count(),
            'en_cours' => (clone $baseQuery)->where('statut', 1)->count(),
            'transmises' => (clone $baseQuery)->where('statut', 2)->count(),
            'validees' => (clone $baseQuery)->where('statut', 3)->count(),
            'rejetees' => (clone $baseQuery)->where('statut', 4)->count(),
            'retardataires' => (clone $baseQuery)->where('statut', '!=', 3)
                ->whereNotNull('date_fin')
                ->where('date_fin', '<', Carbon::today()->toDateString())
                ->count(),
            // Clés rétrocompatibles
            'terminees' => (clone $baseQuery)->where('statut', 3)->count(),
            'suspendues' => (clone $baseQuery)->where('statut', 4)->count(),
        ];

        $versions = Version::all();

        // Assignations (triées par date de création décroissante)
        $assignations = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        // Assignations en retard
        $assignationsEnRetard = (clone $baseQuery)
            ->where('statut', '!=', 3)
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', Carbon::today()->toDateString())
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
     * Valide définitivement une soumission par la DGESS
     */
    public function valider(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->structure?->sigle !== 'DGESS' && !$user->can('assignation-edit')) {
            abort(403, 'Seule la DGESS ou les administrateurs peuvent valider définitivement une soumission.');
        }

        $assignation = Assignation::with(['tableau', 'structure', 'version'])->findOrFail($id);

        if ($assignation->version?->isLocked()) {
            return redirect()->back()->with('error', 'Cette version de l\'Annuaire est verrouillée pour publication officielle. Aucune action n\'est autorisée.');
        }

        $assignation->update([
            'statut' => 3, // Validé / Terminé
            'valide_par' => $user->id,
            'date_validation' => Carbon::now(),
            'motif_rejet' => null,
        ]);

        // Envoi de la notification email à la structure
        $users = User::where('structure_id', $assignation->structure_id)->get();
        foreach ($users as $destinataire) {
            if ($destinataire->email) {
                try {
                    Mail::to($destinataire->email)->send(new ValidationAssignationMail($assignation));
                } catch (\Throwable $e) {
                    Log::warning("Échec envoi mail validation assignation {$assignation->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', "La soumission pour le tableau '{$assignation->tableau->libelle}' a été validée définitivement avec succès par la DGESS.");
    }

    /**
     * Rejette une soumission avec motif pour correction
     */
    public function rejeter(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->structure?->sigle !== 'DGESS' && !$user->can('assignation-edit')) {
            abort(403, 'Seule la DGESS ou les administrateurs peuvent rejeter une soumission.');
        }

        $request->validate([
            'motif_rejet' => 'required|string|min:10|max:2000',
        ], [
            'motif_rejet.required' => 'Le motif du rejet est obligatoire pour orienter la structure.',
            'motif_rejet.min' => 'Le motif du rejet doit comporter au moins 10 caractères.',
        ]);

        $assignation = Assignation::with(['tableau', 'structure', 'version'])->findOrFail($id);

        if ($assignation->version?->isLocked()) {
            return redirect()->back()->with('error', 'Cette version de l\'Annuaire est verrouillée pour publication officielle. Aucune action n\'est autorisée.');
        }

        $assignation->update([
            'statut' => 4, // Rejeté / À corriger
            'motif_rejet' => $request->motif_rejet,
            'rejete_par' => $user->id,
            'date_rejet' => Carbon::now(),
        ]);

        // Envoi de la notification email motivée à la structure
        $users = User::where('structure_id', $assignation->structure_id)->get();
        foreach ($users as $destinataire) {
            if ($destinataire->email) {
                try {
                    Mail::to($destinataire->email)->send(new RejetAssignationMail($assignation, $request->motif_rejet));
                } catch (\Throwable $e) {
                    Log::warning("Échec envoi mail rejet assignation {$assignation->id}: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('warning', "La soumission a été rejetée pour correction. Un email motivé a été transmis à la structure.");
    }

    /**
     * Relance unitaire d'une structure pour une assignation
     */
    public function relancer(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->structure?->sigle !== 'DGESS' && !$user->can('assignation-edit')) {
            abort(403, 'Seule la DGESS ou les administrateurs peuvent émettre des relances.');
        }

        $assignation = Assignation::with(['tableau', 'structure', 'version'])->findOrFail($id);

        if ($assignation->statut === 3) {
            return redirect()->back()->with('info', 'Cette assignation est déjà validée définitivement.');
        }

        $typeRelance = $assignation->isEnRetard() ? 'retard' : 'echeance_proche';

        $users = User::where('structure_id', $assignation->structure_id)->get();
        $emails = $users->pluck('email')->filter()->all();
        if (empty($emails)) {
            $emails = [config('mail.from.address', 'dsi.dgess@mid.gov.bf')];
        }

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new RelanceAssignationMail($assignation, $typeRelance));
            } catch (\Throwable $e) {
                Log::warning("Échec envoi relance assignation {$assignation->id}: " . $e->getMessage());
            }
        }

        $assignation->update([
            'derniere_relance_at' => Carbon::now(),
            'relances_count' => ($assignation->relances_count ?? 0) + 1,
        ]);

        return redirect()->back()->with('success', "Une relance officielle a été envoyée avec succès à {$assignation->structure->nom}.");
    }

    /**
     * Relance groupée de toutes les structures retardataires
     */
    public function relancerRetardataires(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->structure?->sigle !== 'DGESS' && !$user->can('assignation-edit')) {
            abort(403, 'Seule la DGESS ou les administrateurs peuvent émettre des relances groupées.');
        }

        $assignations = Assignation::with(['tableau', 'structure', 'version'])
            ->where('statut', '!=', 3)
            ->whereNotNull('date_fin')
            ->get();

        $candidats = $assignations->filter(function ($a) {
            return $a->isEnRetard() || $a->isEcheanceProche(3);
        });

        if ($candidats->isEmpty()) {
            return redirect()->back()->with('info', "Aucune structure retardataire ou en échéance critique n'a été détectée.");
        }

        $count = 0;
        foreach ($candidats as $assignation) {
            $typeRelance = $assignation->isEnRetard() ? 'retard' : 'echeance_proche';

            $users = User::where('structure_id', $assignation->structure_id)->get();
            $emails = $users->pluck('email')->filter()->all();
            if (empty($emails)) {
                $emails = [config('mail.from.address', 'dsi.dgess@mid.gov.bf')];
            }

            foreach ($emails as $email) {
                try {
                    Mail::to($email)->send(new RelanceAssignationMail($assignation, $typeRelance));
                } catch (\Throwable $e) {
                    Log::warning("Échec envoi relance groupée {$assignation->id}: " . $e->getMessage());
                }
            }

            $assignation->update([
                'derniere_relance_at' => Carbon::now(),
                'relances_count' => ($assignation->relances_count ?? 0) + 1,
            ]);

            $count++;
        }

        return redirect()->back()->with('success', "{$count} relance(s) officielle(s) ont été envoyées aux structures concernées.");
    }
}
