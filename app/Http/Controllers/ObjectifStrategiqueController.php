<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ObjectifStrategiqueRequest;
use App\Models\ObjectifsStrategique;
use App\Models\ProgrammeBudgetaire;
use Illuminate\Support\Facades\DB;

class ObjectifStrategiqueController extends Controller
{
    //
    public function index()
    {
        $objectifStrategiques = ObjectifsStrategique::all();
        $programmeBudgetaires = ProgrammeBudgetaire::all();
        return view('objectifStrategiques.index', compact('objectifStrategiques','programmeBudgetaires'));
    }
    
    // Show objectif strategique
    public function show($id){
        $objectifStrategique = ObjectifsStrategique::findOrFail($id);
        return view('objectifStrategiques.show', compact('objectifStrategique'));
    }

    public function create()
    {
        // Selectionner les programmes budgétaires dans la table programme_budgetaires pour le formulaire
        $programmeBudgetaires = ProgrammeBudgetaire::all();
        return view('objectifStrategiques.create', compact('programmeBudgetaires'));
    }

    public function store(ObjectifStrategiqueRequest $request)
    {
        ObjectifsStrategique::create($request->validated());
        return redirect()->route('objectifStrategiques.index')->with('success', 'Objectif Strategique créé avec succès');
    }

    public function edit($id)
    {
        $objectifStrategique = ObjectifsStrategique::findOrFail($id);
        $programmeBudgetaires = ProgrammeBudgetaire::all();
        return view('objectifStrategiques.edit', compact('objectifStrategique', 'programmeBudgetaires'));
        
    }

    public function update(ObjectifStrategiqueRequest $request, $id)
    {
        $objectifStrategique = ObjectifsStrategique::find($id);
        $objectifStrategique->update($request->validated());
        return redirect()->route('objectifStrategiques.index')->with('success', 'Objectif Strategique mis à jour avec succès');
    }

    public function destroy($id)
    {
        ObjectifsStrategique::destroy($id);
        return redirect()->route('objectifStrategiques.index')->with('success', 'Objectif Strategique supprimé avec succès');
    }
}
