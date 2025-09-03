<?php

namespace App\Http\Controllers;

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
     * Afficher le formulaire de création
     */
      public function create()
    {
     
        $structures = Structure::all();

        return view('programmebudgetaire.create', compact('structures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'numero_programme' => 'required|string|max:255',
           
            'structures' => 'array',
            'structures.*' => 'exists:structures,id'
        ]);

        // Création du programme budgétaire
        $ProgrammeBudgetaire = ProgrammeBudgetaire::create([
            'libelle' => $validated['libelle'],
            'numero_programme' => $validated['numero_programme'],
          
        ]);

        // Attachement des structures sélectionnées
        if (!empty($validated['structures'])) {
          $ProgrammeBudgetaire->structures()->attach($validated['structures']);
        }

        return redirect()->route('programmebudgetaire.index')->with('success', 'Programme budgétaire créé avec succès.');
    }

    /**
     * Afficher un programme budgétaire
     */
    public function show(ProgrammeBudgetaire$ProgrammeBudgetaire)
    {
        return view('programmebudgetaire.show', compact('ProgrammeBudgetaire'));
    }

    /**
     * Afficher le formulaire d’édition
     */
 public function edit($id)
{
  $ProgrammeBudgetaire = ProgrammeBudgetaire::with('structures')->findOrFail($id);
    $structures = Structure::all();

    return view('programmebudgetaire.edit', compact('ProgrammeBudgetaire', 'structures'));
}


    /**
     * Mettre à jour un programme budgétaire
     */
    public function update(Request $request, ProgrammeBudgetaire$ProgrammeBudgetaire)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'numero_programme' => 'required|string|max:100|unique:programmebudgetaire,numero_programme,' .$ProgrammeBudgetaire->numero_programme,
           ' structures' => 'array',
            'structures.*' => 'exists:structures,id'
        ]);

      $ProgrammeBudgetaire->update($request->all());

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire mis à jour avec succès.');
    }

    /**
     * Supprimer un programme budgétaire
     */
    public function destroy(ProgrammeBudgetaire$ProgrammeBudgetaire)
    {
      $ProgrammeBudgetaire->delete();

        return redirect()->route('programmebudgetaire.index')
            ->with('success', 'Programme budgétaire supprimé avec succès.');
    }
}
