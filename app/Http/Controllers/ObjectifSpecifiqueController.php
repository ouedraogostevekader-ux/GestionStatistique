<?php

namespace App\Http\Controllers;

use App\Models\ObjectifSpecifique;
use Illuminate\Http\Request;

class ObjectifSpecifiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $objectifs = ObjectifSpecifique::all();
        return view('obj_specifique.index', compact('objectifs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('objectifs.create');
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

        $objectif = ObjectifSpecifique::create($validatedData);

        return redirect()->route('objectifs.index')->with('success', 'Objectif created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ObjectifSpecifique $objectifSpecifique)
    {
        return view('objectifs.show', compact('objectifSpecifique'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ObjectifSpecifique $objectifSpecifique)
    {
        return view('objectifs.edit', compact('objectifSpecifique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ObjectifSpecifique $objectifSpecifique)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
        ]);

        $objectifSpecifique->update($validatedData);

        return redirect()->route('objectifs.index')->with('success', 'Objectif updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ObjectifSpecifique $objectifSpecifique)
    {
        $objectifSpecifique->delete();

        return redirect()->route('objectifs.index')->with('success', 'Objectif deleted successfully.');
    }
}
