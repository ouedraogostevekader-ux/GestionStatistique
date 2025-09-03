<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\ProgrammeBudgetaire;
use App\Models\Structure;
use App\Http\Controllers\StructureController;

class ProgrammeBudgetaireController extends Controller
{    /**
     * Afficher la liste
     */
    public function index()
    {
        $programmes = ProgrammeBudgetaire::paginate(10);
        return view('programmebudgetaire.index', compact('programmes'));
    }


 /**
     * Afficher un programme budgétaire
     */
    public function show($id)
    {
        $ProgrammeBudgetaire = ProgrammeBudgetaire::with('structures')->findOrFail($id);
        return view('programmebudgetaire.show', compact('ProgrammeBudgetaire'));
    }

        /**
     * Afficher le formulaire de création
     */
    public function create()
    {

        $structures = DB::table('structures')->get();

        return view('programmebudgetaire.create', compact('structures'));
    }

public function store(Request $request)
{
    //dd($request);

    $validated = $request->validate([
        'libelle' => 'required|string|max:255',
        'numero_programme' => 'required|string|max:255',
        //'structures' => 'nullable|array',
        //'structures.*' => 'exists:structures,id'
    ]);

    

    // Création du programme budgétaire
    $programmeBudgetaire = ProgrammeBudgetaire::create([
        'libelle' => $validated['libelle'],
        'numero_programme' => $validated['numero_programme'],
    ]);

    // Attachement des structures sélectionnées (si présentes)
    if (!empty($validated['structures'])) {
        $programmeBudgetaire->structures()->sync($validated['structures']);
    }

    return redirect()->route('programmebudgetaire.index')
                     ->with('success', 'Programme budgétaire créé avec succès.');
}


    /**
     * Afficher le formulaire d’édition
     */
    public function edit($id)
    {
     $ProgrammeBudgetaire = ProgrammeBudgetaire::with('structures')->findOrFail($id);
     $structures = DB::table('structures')->get();
        return view('programmebudgetaire.edit', compact('ProgrammeBudgetaire', 'structures'));
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'libelle' => 'required|string|max:255',
        'numero_programme' => 'required|string|max:255',
        'structures' => 'nullable|array',
        'structures.*' => 'exists:structures,id'
    ]);

    $programmeBudgetaire = ProgrammeBudgetaire::findOrFail($id);

    $programmeBudgetaire->update([
        'libelle' => $validated['libelle'],
        'numero_programme' => $validated['numero_programme'],
    ]);

    // Mise à jour des structures associées
    if (!empty($validated['structures'])) {
        $programmeBudgetaire->structures()->sync($validated['structures']);
    } else {
        $programmeBudgetaire->structures()->detach(); // Si aucune structure sélectionnée
    }

    return redirect()->route('programmebudgetaire.index')
                     ->with('success', 'Programme budgétaire mis à jour avec succès.');
}

    /**
     * Supprimer un programme budgétaire
     */
    public function destroy($id)
    {
      ProgrammeBudgetaire::destroy($id);

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire supprimé avec succès.');
    }
}
