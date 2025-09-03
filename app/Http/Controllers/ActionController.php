<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actions = Action::all();
        //dd($actions);
        return view('action.index', compact('actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('actions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
        ]);

        $action = Action::create($validatedData);

        return redirect()->route('actions.index')->with('success', 'Action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Action $action)
    {
        return view('actions.show', compact('action'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Action $action)
    {
        return view('actions.edit', compact('action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Action $action)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
        ]);

        $action->update($validatedData);

        return redirect()->route('actions.index')->with('success', 'Action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Action $action)
    {
        $action->delete();

        return redirect()->route('actions.index')->with('success', 'Action deleted successfully.');
    }
}
