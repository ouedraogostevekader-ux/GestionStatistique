<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgrammeBudgetaire;

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
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('programmebudgetaire.create');
    }

    /**
     * Enregistrer un nouveau programme budgétaire
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:programmebudgetaire',
           
        ]);

        ProgrammeBudgetaire::create($request->all());

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire créé avec succès.');
    }

    /**
     * Afficher un programme budgétaire
     */
    public function show(ProgrammeBudgetaire $ProgrammeBudgetaire)
    {
        return view('programmebudgetaire.show', compact('ProgrammeBudgetaire'));
    }

    /**
     * Afficher le formulaire d’édition
     */
    public function edit(ProgrammeBudgetaire $ProgrammeBudgetaire)
    {
        return view('programmebudgetaire.edit', compact('ProgrammeBudgetaire'));
    }

    /**
     * Mettre à jour un programme budgétaire
     */
    public function update(Request $request, ProgrammeBudgetaire $ProgrammeBudgetaire)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:programmebudgetaire,code,' . $ProgrammeBudgetaire->id,
           
        ]);

        $ProgrammeBudgetaire->update($request->all());

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire mis à jour avec succès.');
    }

    /**
     * Supprimer un programme budgétaire
     */
    public function destroy(ProgrammeBudgetaire $ProgrammeBudgetaire)
    {
        $ProgrammeBudgetaire->delete();

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire supprimé avec succès.');
    }
}
