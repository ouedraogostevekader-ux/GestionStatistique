<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuiviRequest;
use App\Http\Requests\UpdateSuiviRequest;
use App\Models\Suivi;
use Illuminate\Support\Facades\DB;

class SuiviController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $suivis = Suivi::paginate(10);
        return view('suivis.index', compact('suivis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$indicateurProgrammes = IndicateurProgramme::all();
        $indicateurProgrammes = DB::table('indicateurs_programmes')->get();
        return view('suivis.create', compact('indicateurProgrammes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuiviRequest $request)
    {
        //
        Suivi::create($request->validated());
        return redirect()->route('suivis.index')->with('success', 'Suivi créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        return view('suivis.show', compact('suivi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        //$indicateurProgrammes = IndicateurProgramme::all();
        $indicateurProgrammes = DB::table('indicateurs_programmes')->get();
        $suivi = Suivi::findOrFail($id);
        return view('suivis.edit', compact('suivi', 'indicateurProgrammes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuiviRequest $request, $id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        $suivi->update($request->validated());
        return redirect()->route('suivis.index')->with('success', 'Suivi mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        $suivi->delete();
        return redirect()->route('suivis.index');
    }
}
