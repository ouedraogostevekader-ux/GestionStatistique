<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Activite;
use App\Models\IndicateurProgramme;
use App\Models\ObjectifSpecifique;
use App\Models\ObjectifsStrategique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProgrammeBudgetaire;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ProgrammeBudgetaireController extends Controller implements HasMiddleware
{
    public function __construct()
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:programme_Budgetaire-list', only: ['index', 'show']),
            new Middleware('permission:programme_Budgetaire-create', only: ['create', 'store']),
            new Middleware('permission:programme_Budgetaire-edit', only: ['edit', 'update']),
            new Middleware('permission:programme_Budgetaire-delete', only: ['destroy']),
            // new Middleware('role:Correspondant', only: ['index', 'show']), // Au lieu de isAdminMiddleware
        ];
    }

    /**
     * Afficher la liste des programmes
     */
    public function index(Request $request)
    {
        $query = ProgrammeBudgetaire::with(['structures', 'responsables']);

        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('created_at', $request->annee);
        }

        // Filtre par structure
        if ($request->filled('structure_id')) {
            $query->whereHas('structures', function ($q) use ($request) {
                $q->where('id', $request->structure_id);
            });
        }

        // Filtre par responsable actif
        if ($request->filled('responsable_id')) {
            $query->whereHas('responsables', function ($q) use ($request) {
                $q->where('id', $request->responsable_id)
                    ->whereNull('responsables.date_fin'); // ici la bonne syntaxe
            });
        }

        $programmes = $query->paginate(1000);

        $structures = Structure::orderBy('nom')->get();
        $users = User::orderBy('name')->get();

        return view('programmebudgetaire.index', compact('programmes', 'structures', 'users'));
    }


    /**
     * Afficher les détails d’un programme
     */
    public function show($id)
    {
        $programmeBudgetaire = ProgrammeBudgetaire::with([
            'structures',
            'responsables' => function ($q) {
                $q->wherePivotNull('date_fin');
            }
        ])->findOrFail($id);

        $responsableActif = $programmeBudgetaire->responsables->last();

        return view('programmebudgetaire.show', compact('programmeBudgetaire', 'responsableActif'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $structures = Structure::orderBy('nom')->get();
        $users = User::orderBy('name')->get();

        return view('programmebudgetaire.create', compact('structures', 'users'));
    }

    /**
     * Enregistrer un nouveau programme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'numero_programme' => 'required|string|max:255|unique:programmes_budgetaires,numero_programme',
            'responsable_id' => 'required|exists:users,id',
            'structures' => 'nullable|array',
            'structures.*' => 'exists:structures,id',

        ]);

        DB::transaction(function () use ($validated) {
            $programmeBudgetaire = ProgrammeBudgetaire::create([
                'libelle' => $validated['libelle'],
                'numero_programme' => $validated['numero_programme'],
                'isActive' => true, // actif par défaut
            ]);

            // Affecter le responsable actif
            $programmeBudgetaire->responsables()->attach($validated['responsable_id'], [
                'date_debut' => now(),
                'date_fin' => null,
            ]);

            // Associer les structures
            if (!empty($validated['structures'])) {
                $programmeBudgetaire->structures()->sync($validated['structures']);
            }
        });

        return redirect()->route('programmebudgetaire.index');
    }

    /**
     * Afficher le formulaire d’édition
     */

    public function edit($id)
    {

        $programmeBudgetaire = ProgrammeBudgetaire::with(['structures', 'responsables'])->findOrFail($id);

        $structures = Structure::orderBy('nom')->get();
        $users = User::orderBy('name')->get();


        // Responsable actif actuel
        $responsableActif = $programmeBudgetaire->responsables->firstWhere('pivot.date_fin', null);

        return view('programmebudgetaire.edit', compact('programmeBudgetaire', 'structures', 'users', 'responsableActif'));
    }

    /**
     * Mettre à jour un programme existant
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate(['libelle' => 'required|string|max:255', 'numero_programme' => 'required|string|max:255', 'responsable_id' => 'required|exists:users,id', 'structures' => 'nullable|array', 'structures.*' => 'exists:structures,id',]);

        DB::transaction(function () use ($validated, $id) {
            $programmeBudgetaire = ProgrammeBudgetaire::with('responsables')->findOrFail($id);

            $responsableActif = $programmeBudgetaire->responsables->firstWhere('pivot.date_fin', null);

            // Si le responsable change
            if (!$responsableActif || $responsableActif->id !== $validated['responsable_id']) {
                // Clôturer l’ancien responsable actif
                if ($responsableActif) {
                    $programmeBudgetaire->responsables()->updateExistingPivot($responsableActif->id, [
                        'date_fin' => now()
                    ]);
                }

                // Affecter le nouveau responsable
                $programmeBudgetaire->responsables()->attach($validated['responsable_id'], [
                    'date_debut' => now(),
                    'date_fin' => null,
                ]);
            }

            // Mettre à jour les champs principaux
            $programmeBudgetaire->update([
                'libelle' => $validated['libelle'],
                'numero_programme' => $validated['numero_programme'],
            ]);

            // Mettre à jour les structures
            if (!empty($validated['structures'])) {
                $programmeBudgetaire->structures()->sync($validated['structures']);
            } else {
                $programmeBudgetaire->structures()->detach();
            }
        });

        return redirect()->route('programmebudgetaire.index');
    }

    /**
     * Supprimer un programme
     */
    public function destroy($id)
    {
        $programmeBudgetaire = ProgrammeBudgetaire::findOrFail($id);
        $programmeBudgetaire->delete();
        return redirect()->route('programmebudgetaire.index');

        if (!$programmeBudgetaire->isActive) {
            return redirect()->route('programmebudgetaire.index')
                ->withErrors(['error' => 'Ce programme est verrouillé et ne peut pas être supprimé.']);
        }
    }

    /* Valider le programme */
    public function validate($id)
    {
        $programmeBudgetaire = ProgrammeBudgetaire::findOrFail($id);
        $responsable = $programmeBudgetaire->responsables->last();
        //dd(Auth::user(), $responsable);
        if (Auth::user()->id !== $responsable->id) {
            return redirect()->back()->with('error', "Vous n'êtes pas responsable de ce programme. Donc vous ne pouvez pas le valider!");

        }
        $programmeBudgetaire->statut = 1;
        $programmeBudgetaire->isActive = false;
        $programmeBudgetaire->save();
        return redirect()->back()->with('success', "Ce programme bugétaire a été validé.");
    }

    // Afficher le formulaire de duplication
    public function createDuplicate()
    {
        $programmes = ProgrammeBudgetaire::with(['objectifsStrategiques.actions.objectifsSpecifiques.activites.periodes'])
            ->where('isActive', true)
            ->get();

        return view('programmebudgetaire.duplicate', compact('programmes'));
    }

    // Traiter la duplication
    public function storeDuplicate(Request $request)
    {
        $request->validate([
            // 'programme_source_id' => 'required|exists:programmes_budgetaires,id',
            //'nouveau_libelle' => 'required|string|max:255',
            // 'nouveau_numero' => 'required|string|max:50',
            'programme_source_id' => 'required|array',
            'annee' => 'required|integer|min:2000|max:2100',
            'dupliquer_activites' => 'sometimes|boolean'
        ]);
        //dd($request->all());
        DB::beginTransaction();
        try {
            foreach ($request->programme_source_id as $sourceId) {
                $programmeSource = ProgrammeBudgetaire::with([
                    'objectifsStrategiques.actions.objectifsSpecifiques.activites.periodes'
                ])->findOrFail($sourceId);
                // ✅ Vérification avant duplication
                $existe = ProgrammeBudgetaire::where('numero_programme', $programmeSource->numero_programme)
                    ->where('annee', $request->annee)
                    ->exists();

                if ($existe) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', "Le programme « {$programmeSource->libelle} » a déjà été dupliqué pour l'année {$request->annee}. Veuillez ressayer SVP")
                        ->withInput();
                }
                $nouveauProgramme = ProgrammeBudgetaire::create([
                    'id' => Str::ulid(),
                    'libelle' => $programmeSource->libelle,
                    'numero_programme' => $programmeSource->numero_programme,
                    'annee' => $request->annee,
                    'isActive' => true,
                    //'programme_activite_id' => $programmeSource->programme_activite_id
                ]);

                $this->dupliquerHierarchie(
                    $programmeSource,
                    $nouveauProgramme,
                    $request->annee,
                    $request->dupliquer_activites
                );
            }
            DB::commit();
            return redirect()->route('programmebudgetaire.index')
                ->with('success', 'Programme(s) budgétaire(s) dupliqué(s) avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la duplication: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function dupliquerHierarchie($programmeSource, $nouveauProgramme, $annee, $dupliquerActivites)
    {
        foreach ($programmeSource->objectifsStrategiques as $osSource) {
            // Dupliquer objectif stratégique
            $nouvelOS = ObjectifsStrategique::create([
                'id' => Str::ulid(),
                'libelle' => $osSource->libelle,
                'annee' => $annee,
                'programme_budgetaire_id' => $nouveauProgramme->id
            ]);
            //Indicateurs
            foreach ($osSource->indicateurs as $indicateur) {
                IndicateurProgramme::create([
                    'libelle' => $indicateur->libelle,
                    'unite_mesure' => $indicateur->unite_mesure,
                    'valeur_ref' => $indicateur->valeur_ref,
                    'valeur_cible' => $indicateur->valeur_cible,
                    'annee' => $annee,
                    'objectif_strategique_id' => $osSource->id,
                ]);
            }

            foreach ($osSource->actions as $actionSource) {
                // Dupliquer action
                $nouvelleAction = Action::create([
                    'id' => Str::ulid(),
                    'libelle' => $actionSource->libelle,
                    'annee' => $annee,
                    'objectif_strategique_id' => $nouvelOS->id
                ]);

                foreach ($actionSource->objectifsSpecifiques as $osSpecSource) {
                    // Dupliquer objectif spécifique
                    $nouvelOSSpec = ObjectifSpecifique::create([
                        'id' => Str::ulid(),
                        'libelle' => $osSpecSource->libelle,
                        'annee' => $annee,
                        'action_id' => $nouvelleAction->id
                    ]);

                    // Dupliquer les activités si demandé
                    if ($dupliquerActivites) {
                        foreach ($osSpecSource->activites as $activiteSource) {
                            $nouvelleActivite = Activite::create([
                                'id' => Str::ulid(),
                                'libelle' => $activiteSource->libelle,
                                'cout_previsionnel' => $activiteSource->cout_previsionnel,
                                'annee' => $annee,
                                'objectif_specifique_id' => $nouvelOSSpec->id
                            ]);

                            // Dupliquer les périodes associées
                            if ($activiteSource->periodes->count() > 0) {
                                $periodesData = [];
                                foreach ($activiteSource->periodes as $periode) {
                                    $periodesData[$periode->id] = ['annee' => $annee];
                                }
                                $nouvelleActivite->periodes()->attach($periodesData);
                            }
                        }
                    }
                }
            }
        }
    }

    // public function toggleGlobalEditable()
    // {

    //     // Vérifie l'état actuel
    //     $locked = ProgrammeBudgetaire::where('isActive', false)->exists();

    //     // Inverse tous les programmes
    //     ProgrammeBudgetaire::query()->update(['isActive' => $locked ? true : false]);

    //     return redirect()->route('programmebudgetaire.index')
    //                      ->with('success', $locked ? 'Tous les programmes sont activés.' : 'Toutes les modifications sont désactivées.');
    // }

    public function toggleEditable($id)
    {
        $programme = ProgrammeBudgetaire::findOrFail($id);

        $programme->isActive = !$programme->isActive;
        $programme->save();

        return redirect()->route('programmebudgetaire.index');
    }
}
