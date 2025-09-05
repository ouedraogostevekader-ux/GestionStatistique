<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProgrammeBudgetaire;
use App\Models\Structure;
use App\Models\User;


class ProgrammeBudgetaireController extends Controller
{



    /**
     * Afficher la liste des programmes
     */
    public function index()
    {
        // Charger les programmes avec structures + responsable actif
        $programmes = ProgrammeBudgetaire::with([
            'structures',
            'responsables' => function ($q) {
                $q->wherePivotNull('date_fin'); // uniquement responsables actifs
            }
            
        ])->paginate(10);
        
    $locked = ProgrammeBudgetaire::where('isActive', false)->exists();

        return view('programmebudgetaire.index', compact('programmes', 'locked'));
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
        $validated = $request->validate([ 'libelle' => 'required|string|max:255', 'numero_programme' => 'required|string|max:255', 'responsable_id' => 'required|exists:users,id', 'structures' => 'nullable|array', 'structures.*' => 'exists:structures,id', ]);

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
      $programmeBudgetaire->delete();
      return redirect()->route('programmebudgetaire.index')
                       ->with('success', 'Programme budgétaire supprimé avec succès.');
                       if (!$programmeBudgetaire->isActive) {
    return redirect()->route('programmebudgetaire.index')
                     ->withErrors(['error' => 'Ce programme est verrouillé et ne peut pas être supprimé.']);
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

    return redirect()->route('programmebudgetaire.index')
                     ->with('success', $programme->isActive ? 
                        "Le programme {$programme->libelle} est déverrouillé." : 
                        "Le programme {$programme->libelle} est verrouillé.");
}



}
