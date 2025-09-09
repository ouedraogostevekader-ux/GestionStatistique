<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class AssignationController extends Controller
{
    public function index()
    {
        $assignations = Assignation::with(['tableau', 'structure'])->paginate(10);
        return view('assignations.index', compact('assignations'));
    }

    public function create()
    {
        $tableaux = Tableau::all();
        $structures = Structure::all();
        return view('assignations.create', compact('tableaux', 'structures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tableau_id' => 'required|exists:tableaux,id',
            'structure_id' => 'required|exists:structures,id',
            'version' => 'nullable|string|max:250',
            'source' => 'nullable|string|max:250',
            'statut' => 'nullable|string|max:250',
            'responsable' => 'nullable|string|max:250',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|string|max:250',
            'annee' => 'nullable|date',
            'periode' => 'nullable|string|max:250'
        ]);

        Assignation::create([
            'id' => Str::ulid(),
            'tableau_id' => $request->tableau_id,
            'structure_id' => $request->structure_id,
            'version' => $request->version,
            'source' => $request->source,
            'statut' => $request->statut,
            'responsable' => $request->responsable,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'annee' => $request->annee,
            'periode' => $request->periode
        ]);

        return redirect()->route('assignations.index')
                         ->with('success', 'Assignation créée avec succès');
    }
}
