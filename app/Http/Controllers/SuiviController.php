<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuiviRequest;
use App\Http\Requests\UpdateSuiviRequest;
use App\Models\Action;
use App\Models\Activite;
use App\Models\IndicateurProgramme;
use App\Models\ObjectifSpecifique;
use App\Models\ObjectifsStrategique;
use App\Models\Periode;
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
        //$suivis = Suivi::all();
        $indicateurProgrammes = collect();
        $activites = collect();
        $objectifSpecifiques = collect();
        $objectifStrategiques = collect();
        $annee = date('Y');
        // si l'utilisateur n'est pas admin, on filtre les indicateurs par la structure de l'utilisateur
        if (!Auth::user()->hasRole('Super-Admin')) {
            $objectifSpecifiques = ObjectifSpecifique::with('action.objectifStrategique')->where('annee', $annee)->whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->orderBy('libelle')->get();
            $objectifStrategiques = ObjectifsStrategique::where('annee', $annee)->whereHas('programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->orderBy('libelle')->get();
            $activites = Activite::with('objectifSpecifique')->where('annee', $annee)->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->orderBy('libelle')->get();

            $actions = Action::with('objectifStrategique')->where('annee', $annee)->whereHas('objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })->orderBy('libelle')->get();
            
            // Récupérer tous les indicateurs avec leurs relations si l'indicateur appartient aux objectifs spécifiques, stratégiques ou activités filtrés
            $indicateurProgrammes = IndicateurProgramme::with([
                'objectifStrategique',
                'objectifSpecifique', 
                'activite',
                'suivis.getPeriode'
            ])->where(function ($query) use ($objectifSpecifiques, $objectifStrategiques, $activites) {
                $query->whereIn('objectif_specifique_id', $objectifSpecifiques->pluck('id'))
                      ->orWhereIn('objectif_strategique_id', $objectifStrategiques->pluck('id'))
                      ->orWhereIn('activites_id', $activites->pluck('id'));
            })->get();    
        }else{
            $indicateurProgrammes = IndicateurProgramme::with([
                'objectifStrategique',
                'objectifSpecifique', 
                'activite',
                'suivis.getPeriode'
            ])->where('annee', $annee)->get();
            $activites = Activite::with('objectifSpecifique')->where('annee', $annee)->orderBy('libelle')->get();
            $objectifSpecifiques = ObjectifSpecifique::with('action.objectifStrategique')->where('annee', $annee)->orderBy('libelle')->get();
            $objectifStrategiques = ObjectifsStrategique::where('annee', $annee)->get();
            $actions = Action::with('objectifStrategique')->where('annee', $annee)->orderBy('libelle')->get();
        }
        
        
        return view('suivis.index', compact('indicateurProgrammes', 'activites', 'objectifSpecifiques', 'objectifStrategiques', 'actions'));
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
        $periodes = Periode::pluck('libelle', 'id');
        return view('suivis.modal-content', ['indicateur_programme_id' => $id,'periodes'=>$periodes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuiviRequest $request)
    {
        //
        // si le suivi existe déjà pour la même période, on retourne une erreur
        if (Suivi::where('indicateur_programme_id', $request->indicateur_programme_id)->where('periode', $request->periode)->exists()) {
            if ($request->ajax()) {
                throw new \Exception('Un suivi pour cet indicateur et cette periode existe déjà');
                return response()->json([
                    'success' => false,
                    'message' => 'Un suivi pour cet indicateur et cette periode existe déjà'
                ]);
            }
            return redirect()->back()->with('error', 'Un suivi pour cet indicateur et cette periode existe deja');
        }
        // si l'indicateur est lié à une activité, on vérifie que la période du suivi est cohérente avec les périodes de l'activité
        $indicateur = IndicateurProgramme::find($request->indicateur_programme_id);
        if ($indicateur && $indicateur->activite) {
            if (!in_array($request->periode, $indicateur->activite->periodes->pluck('id')->toArray())) {
                if ($request->ajax()) {
                    throw new \Exception('La periode du suivi doit correspondre aux periodes de l\'activité');
                    return response()->json([
                        'success' => false,
                        'message' => 'La periode du suivi doit correspondre aux periodes de l\'activité'
                    ]);
                }
                return redirect()->back()->with('error', 'La periode du suivi doit correspondre aux periodes de l\'activité');
            }
        }

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
        $periodes = Periode::pluck('libelle', 'id');
        return view('suivis.modal-content-edit', ['suivi' => $suivi,'periodes'=>$periodes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuiviRequest $request, $id)
    {
        //
        $suivi = Suivi::findOrFail($id);

        $suiviExi = Suivi::where('indicateur_programme_id', $request->indicateur_programme_id)->where('periode', $request->periode)->first();

        if ($suiviExi && $suiviExi->id != $id) {
            if ($request->ajax()) {
                throw new \Exception('Un suivi pour cet indicateur et cette periode existe déjà');
                return response()->json([
                    'success' => false,
                    'message' => 'Un suivi pour cet indicateur et cette periode existe déjà'
                ]);
            }
            return redirect()->back()->with('error', 'Un suivi pour cet indicateur et cette periode existe deja');
        }
        // si l'indicateur est lié à une activité, on vérifie que la période du suivi est cohérente avec les périodes de l'activité
        $indicateur = IndicateurProgramme::find($request->indicateur_programme_id);
        if ($indicateur && $indicateur->activite) {
            if (!in_array($request->periode, $indicateur->activite->periodes->pluck('id')->toArray())) {
                if ($request->ajax()) {
                    throw new \Exception('La periode du suivi doit correspondre aux periodes de l\'activité');
                    return response()->json([
                        'success' => false,
                        'message' => 'La periode du suivi doit correspondre aux periodes de l\'activité'
                    ]);
                }
                return redirect()->back()->with('error', 'La periode du suivi doit correspondre aux periodes de l\'activité');
            }
        }


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
