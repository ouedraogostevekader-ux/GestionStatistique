<?php

namespace App\Http\Controllers;

use App\Models\IndicateurProgramme;
use App\Models\ObjectifSpecifique;
use App\Models\ObjectifsStrategique;
use App\Models\Activite;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IndicateurProgrammeController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:indicateur_Programme-list', only: ['index', 'show']),
            new Middleware('permission:indicateur_Programme-create', only: ['create', 'store']),
            new Middleware('permission:indicateur_Programme-edit', only: ['edit', 'update']),
            new Middleware('permission:indicateur_Programme-delete', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $query = IndicateurProgramme::with(['objectifSpecifique', 'objectifStrategique', 'activite']);

    // Filtrer par objectif spécifique
    if ($request->filled('objectif_specifique_id')) {
        $query->where('objectif_specifique_id', $request->objectif_specifique_id);
    }

    // Filtrer par objectif stratégique
    if ($request->filled('objectif_strategique_id')) {
        $query->where('objectif_strategique_id', $request->objectif_strategique_id);
    }

    // Filtrer par activité
    if ($request->filled('activites_id')) {
        $query->where('activites_id', $request->activites_id);
    }

    $indicateurs = $query->get();

    // Pour alimenter les selects
   $objectifsSpecifiques = ObjectifSpecifique::all();
        $objectifsStrategiques = ObjectifsStrategique::all();
        $activites = Activite::all();

    return view('ind_programme.index', compact(
        'indicateurs',
        'objectifsSpecifiques',
        'objectifsStrategiques',
        'activites'
    ));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $objectifsSpecifiques = ObjectifSpecifique::all();
        $objectifsStrategiques = ObjectifsStrategique::all();
        $activites = Activite::all();
        return view('ind_programme.create', compact('objectifsSpecifiques', 'objectifsStrategiques', 'activites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'libelle' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:255',
            'valeur_ref' => 'required',
            'valeur_cible' => 'required',
            'objectif_specifique_id' => 'nullable|exists:objectifs_specifiques,id',
            'objectif_strategique_id' => 'nullable|exists:objectifs_strategiques,id',
            'activites_id' => 'nullable|exists:activites,id',
        ]);
        $indicateur = IndicateurProgramme::create($validatedData);
        if ($request->indicateur) {
            return redirect()->back()
                ->with('success', 'L\'Objet est créé avec succès.');
        }
        return redirect()->route('indicateurs-programmes.index')->with('success', 'Indicateur created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $indicateur = IndicateurProgramme::findOrFail($id);
        $indicateur = $indicateur->load([
            'objectifSpecifique',
            'objectifStrategique',
            'activite',
            'suivis'
        ]);

        return view('ind_programme.show', compact('indicateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $indicateur = IndicateurProgramme::findOrFail($id);
        $objectifsSpecifiques = ObjectifSpecifique::all();
        $objectifsStrategiques = ObjectifsStrategique::all();
        $activites = Activite::all();

        return view('ind_programme.edit', compact('indicateur', 'objectifsSpecifiques', 'objectifsStrategiques', 'activites'));
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
            'objectif_specifique_id' => 'nullable|exists:objectifs_specifiques,id',
            'objectif_strategique_id' => 'nullable|exists:objectifs_strategiques,id',
            'activites_id' => 'nullable|exists:activites,id',
        ]);

        $indicateurProgramme->update($validatedData);

        return redirect()->route('indicateurs-programmes.index')->with('success', 'Indicateur updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $indicateur = IndicateurProgramme::findOrFail($id);
        $indicateur->delete();

        return redirect()->route('indicateurs-programmes.index')->with('success', 'Indicateur deleted successfully.');
    }
}
