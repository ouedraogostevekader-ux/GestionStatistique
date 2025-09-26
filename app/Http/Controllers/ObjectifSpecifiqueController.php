<?php

namespace App\Http\Controllers;

use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\Periode;
use App\Models\ProgrammeBudgetaire;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ObjectifSpecifiqueController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:objectif_Specifique-list', only: ['index', 'show']),
            new Middleware('permission:objectif_Specifique-create', only: ['create', 'store']),
            new Middleware('permission:objectif_Specifique-edit', only: ['edit', 'update']),
            new Middleware('permission:objectif_Specifique-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $objectifsSpecifiques = ObjectifSpecifique::with(['action.objectifStrategique', 'activites'])->get();

        if ($request->action_id) {
            $objectifsSpecifiques = ObjectifSpecifique::where('action_id', $request->action_id)
                ->get();
            // dd($request->action_id);
        }

        if ($request->annee) {
            $objectifsSpecifiques = ObjectifSpecifique::where('annee', $request->annee)
                ->get();
            // dd($request->action_id);
        }

        if ($request->programme_budgetaire_id) {
            $objectifsSpecifiques = ObjectifSpecifique::whereHas('action.objectifStrategique', function ($query) use ($request) {
                $query->where('programme_budgetaire_id', $request->programme_budgetaire_id);
            })->get();
            // dd($objectifsSpecifiques);
        }
        // dd($request->action_id);

        $programmes = ProgrammeBudgetaire::all();
        $actions = Action::all();

        return view('obj_specifique.index', compact('objectifsSpecifiques', 'programmes', 'actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $actions = Action::all();
        return view('obj_specifique.create', compact('actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'action_id' => 'required|exists:actions,id',
        ]);

        $objectif = ObjectifSpecifique::create($validatedData);
        if ($request->os) {
            return redirect()->back()
                ->with('success', 'Objectif spécifique créé avec succès.');
        }
        return redirect()->route('objectifs-specifiques.index')
            ->with('success', 'Objectif spécifique créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $objectifSpecifique = ObjectifSpecifique::findOrFail($id);
        $objectifSpecifique->load(['action.objectifStrategique', 'activites', 'indicateursProgrammes']);
        $periodes = Periode::all();
        return view('obj_specifique.show', compact('objectifSpecifique', 'periodes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $objectifSpecifique = ObjectifSpecifique::findOrFail($id);
        $actions = Action::all();
        return view('obj_specifique.edit', compact('objectifSpecifique', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'action_id' => 'required|exists:actions,id',
        ]);

        $objectifSpecifique = ObjectifSpecifique::findOrFail($id);

        $objectifSpecifique->update($validatedData);

        return redirect()->route('objectifs-specifiques.index')
            ->with('success', 'Objectif spécifique mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $objectifSpecifique = ObjectifSpecifique::findOrFail($id);
        $objectifSpecifique->delete();

        return redirect()->route('objectifs-specifiques.index')->with('success', 'Objectif spécifique supprimé avec succès.');
    }
}
