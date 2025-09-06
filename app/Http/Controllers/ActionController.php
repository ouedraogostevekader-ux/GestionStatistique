<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ObjectifsStrategique;
use App\Models\ProgrammeBudgetaire;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:action-list', only: ['index', 'show']),
            new Middleware('permission:action-create', only: ['create', 'store']),
            new Middleware('permission:action-edit', only: ['edit', 'update']),
            new Middleware('permission:action-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (Auth::user()->isAdmin()) {
            $query = Action::with(['objectifStrategique.programmeBudgetaire.structures']);
            $programmes = ProgrammeBudgetaire::all();
            $structures = Structure::all();
        } else {
            $query = Action::with(['objectifStrategique.programmeBudgetaire.structures'])
                ->whereHas('objectifStrategique.programmeBudgetaire.structures', function ($q) {
                    $q->where('structures.id', Auth::user()->structure_id);
                });

            $programmes = ProgrammeBudgetaire::whereHas('structures', function ($q) use ($user) {
                $q->where('structures.id', Auth::user()->structure_id);
            })->get();
            $structures = Structure::where('id', Auth::user()->structure_id)->get();
        }

        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('created_at', $request->annee);
        }

        // Filtre par structure
        if ($request->filled('programme_budgetaire_id')) {
            $query->whereHas('objectifStrategique', function ($q) use ($request) {
                $q->where('programme_budgetaire_id', $request->programme_budgetaire_id);
            });
        }

        // Filtre par responsable actif
        if ($request->filled('structure_id')) {
            $query->whereHas('objectifStrategique.programmeBudgetaire.structures', function ($q) use ($request) {
                $q->where('structures.id', $request->structure_id);
            });
        }

        $actions = $query->get();

        return view('action.index', compact('actions', 'structures', 'programmes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $objectifsStrategiques = ObjectifsStrategique::all();
        return view('action.create', compact('objectifsStrategiques'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'objectif_strategique_id' => 'required|exists:objectifs_strategiques,id',
        ]);

        $action = Action::create($validatedData);
        if ($request->action) {
            return redirect()->back()
                ->with('success', 'Objectif spécifique créé avec succès.');
        }
        return redirect()->route('actions.index')->with('success', 'Action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Action $action)
    {
        return view('action.show', compact('action'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Action $action)
    {
        $objectifsStrategiques = ObjectifsStrategique::all();
        return view('action.edit', compact('action', 'objectifsStrategiques'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Action $action)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'objectif_strategique_id' => 'required|exists:objectifs_strategiques,id',
        ]);

        $action->update($validatedData);

        return redirect()->route('actions.index')->with('success', 'Action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $action = Action::findOrFail($id);
        $action->delete();

        return redirect()->route('actions.index')->with('success', 'Action deleted successfully.');
    }
}
