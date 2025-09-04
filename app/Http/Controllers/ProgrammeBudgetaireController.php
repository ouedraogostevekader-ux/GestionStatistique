<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProgrammeBudgetaire;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProgrammeBudgetaireController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:programme_Budgetaire-list', only: ['index', 'show']),
            new Middleware('permission:programme_Budgetaire-create', only: ['create', 'store']),
            new Middleware('permission:programme_Budgetaire-edit', only: ['edit', 'update']),
            new Middleware('permission:programme_Budgetaire-delete', only: ['destroy']),
        ];
    }

    /**
     * Afficher la liste
     */
    public function index()
    {
        $programmes = ProgrammeBudgetaire::paginate(10);

        return view('programmebudgetaire.index', compact('programmes'));
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
            'code' => 'required|string|max:100|unique:programmebudgetaire',

        ]);

        DB::transaction(function () use ($validated) {
            $programmeBudgetaire = ProgrammeBudgetaire::create([
                'libelle' => $validated['libelle'],
                'numero_programme' => $validated['numero_programme'],
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

        return redirect()->route('programmebudgetaire.index')
                         ->with('success', 'Programme budgétaire créé avec succès.');
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
        $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:programmebudgetaire,code,' . $ProgrammeBudgetaire->id,

        ]);

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

        return redirect()->route('programmebudgetaire.index')
                         ->with('success', 'Programme budgétaire mis à jour avec succès.');
    }

    /**
     * Supprimer un programme
     */
    public function destroy($id)
    {
        $programmeBudgetaire = ProgrammeBudgetaire::findOrFail($id);

        DB::transaction(function () use ($programmeBudgetaire) {
            // Détacher les relations pivots avant suppression (si pas en cascade en BDD)
            $programmeBudgetaire->structures()->detach();
            $programmeBudgetaire->responsables()->detach();
            $programmeBudgetaire->delete();
        });

        return redirect()->route('programmebudgetaire.index')
                         ->with('success', 'Programme budgétaire supprimé avec succès.');
    }
}
