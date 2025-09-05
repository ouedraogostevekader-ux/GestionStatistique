<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuiviRequest;
use App\Http\Requests\UpdateSuiviRequest;
use App\Models\Activite;
use App\Models\IndicateurProgramme;
use App\Models\ObjectifSpecifique;
use App\Models\ObjectifsStrategique;
use App\Models\Suivi;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuiviController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:suivi-list', only: ['index', 'show']),
            new Middleware('permission:suivi-create', only: ['create', 'store']),
            new Middleware('permission:suivi-edit', only: ['edit', 'update']),
            new Middleware('permission:suivi-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $suivis = Suivi::all();
        $indicateurProgrammes = collect();
        $activites = collect();
        $objectifSpecifiques = collect();
        $objectifStrategiques = collect();
        // si l'utilisateur n'est pas admin, on filtre les indicateurs par la structure de l'utilisateur
        if (!Auth::user()->hasRole('Super-Admin')) {
            $objectifSpecifiques = ObjectifSpecifique::whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->get();
            $objectifStrategiques = ObjectifsStrategique::whereHas('programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->get();
            $activites = Activite::whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->get();
            // Récupérer les IDs des indicateurs liés aux objectifs spécifiques, objectifs stratégiques et activités
            $indicateursSpe = $objectifSpecifiques->indicateurs;
            $indicateursStrat = $objectifStrategiques->indicateurs;
            $indicateursAct = $activites->indicateurs;
            $indicateurProgrammes = $indicateursSpe->merge($indicateursStrat)->merge($indicateursAct);
            
        }else{
            $indicateurProgrammes = IndicateurProgramme::all();
            $activites = Activite::all();
            $objectifSpecifiques = ObjectifSpecifique::all();
            $objectifStrategiques = ObjectifsStrategique::all();
        }
        
        
        return view('suivis.index', compact('suivis', 'indicateurProgrammes', 'activites', 'objectifSpecifiques', 'objectifStrategiques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $indicateurProgrammes = IndicateurProgramme::all();
        return view('suivis.create', compact('indicateurProgrammes'));
    }

    public function createByMIndicateur($id)
    {
        $periodes = ['T1'=>'T1', 'T2'=>'T2', 'T3'=>'T3', 'T4'=>'T4'];
        return view('suivis.modal-content', ['indicateur_programme_id' => $id,'periodes'=>$periodes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuiviRequest $request)
    {
        //
        $suivi =Suivi::create($request->validated());
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Suivi créé avec succès!'
            ]);
        }
        
        return redirect()->route('suivis.index')->with('success', 'Suivi créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        if ($request->ajax()) {
            return response()->json($suivi->load('indicateurProgramme'));
        }
        
        return view('suivis.show', compact('suivi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        $periodes = ['T1'=>'T1', 'T2'=>'T2', 'T3'=>'T3', 'T4'=>'T4'];
        return view('suivis.modal-content-edit', ['suivi' => $suivi,'periodes'=>$periodes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuiviRequest $request, $id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        $suivi->update($request->validated());
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Suivi mos à jour avec succès!'
            ]);
        }
        return redirect()->route('suivis.index')->with('success', 'Suivi mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        //
        $suivi = Suivi::findOrFail($id);
        $suivi->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Suivi supprimé avec succès!'
            ]);
        }
        return redirect()->route('suivis.index');
    }
}
