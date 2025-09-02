<?php

namespace App\Http\Controllers;

use App\Models\IndicateurProgramme;
use Illuminate\Http\Request;

class IndicateurProgrammeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indicateurs = IndicateurProgramme::all();
        return view('ind_programme.index', compact('indicateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('indicateurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:255',
            'valeur_ref' => 'required|numeric',
            'valeur_cible' => 'required|numeric',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
            'activites_id' => 'required|exists:activites,id',
        ]);

        $indicateur = IndicateurProgramme::create($validatedData);

        return redirect()->route('indicateurs.index')->with('success', 'Indicateur created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IndicateurProgramme $indicateurProgramme)
    {
        return view('indicateurs.show', compact('indicateurProgramme'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndicateurProgramme $indicateurProgramme)
    {
        return view('indicateurs.edit', compact('indicateurProgramme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndicateurProgramme $indicateurProgramme)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:255',
            'valeur_ref' => 'required|numeric',
            'valeur_cible' => 'required|numeric',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
            'activites_id' => 'required|exists:activites,id',
        ]);

        $indicateurProgramme->update($validatedData);

        return redirect()->route('indicateurs.index')->with('success', 'Indicateur updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndicateurProgramme $indicateurProgramme)
    {
        $indicateurProgramme->delete();

        return redirect()->route('indicateurs.index')->with('success', 'Indicateur deleted successfully.');
    }
}
