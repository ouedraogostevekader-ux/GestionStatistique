<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateActiviteRequest;
use App\Models\Activite;
use App\Models\ObjectifSpecifique;
use App\Models\Periode;
use App\Repositories\ActiviteRepository;
use DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ActiviteController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:activite-list', only: ['index', 'show']),
            new Middleware('permission:activite-create', only: ['create', 'store']),
            new Middleware('permission:activite-edit', only: ['edit', 'update']),
            new Middleware('permission:activite-delete', only: ['destroy']),
        ];
    }
    protected $activiteRepository;

    public function __construct(ActiviteRepository $activiteRepository)
    {
        $this->activiteRepository = $activiteRepository;
    }

    public function index()
    {
        // dd(Auth::user()->structure->programmesBudgetaires[0]->objectifsStrategiques);
        if (Auth::user()->isAdmin()) {
            $objectifs = DB::select("SELECT * from objectifs_specifiques");
            $activites = $this->activiteRepository->all();
        } else {
            // Via la table pivot structure_programme_budgetaire
            $objectifs = ObjectifSpecifique::whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->get();

            $activites = Activite::whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->get();
        }
        return view('activites.index', compact('activites', 'objectifs'));
    }

    /* Create Activite */
    public function create()
    {
        if (Auth::user()->isAdmin()) {
            $objectifs = DB::select("SELECT * from objectifs_specifiques");
        } else {
            // Via la table pivot structure_programme_budgetaire
            $objectifs = ObjectifSpecifique::whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->get();
        }
        $periodes = Periode::all();
        return view('activites.create', compact('objectifs', 'periodes'));
    }

    public function store(StoreActiviteRequest $request)
    {
        $validated = $request->validated();
        // Créer l'activité
        $activite = $this->activiteRepository->create($validated);

        // Attacher les périodes sélectionnées
        if (isset($validated['periodes']) && !empty($validated['periodes'])) {
            $periodesData = [];
            foreach ($validated['periodes'] as $periodeId) {
                $periodesData[$periodeId] = ['annee' => $validated['annee']];
            }
            $activite->periodes()->attach($periodesData);
        }
        if ($request->activite) {
            return redirect()->back()
                ->with('success', 'Objectif spécifique créé avec succès.');
        }
        return redirect()->route('activites.index')->with('success', "L'activité " . $activite->libelle . " a été enregistrée.");
    }

    public function edit($ulid)
    {
        if (Auth::user()->isAdmin()) {
            $objectifs = DB::select("SELECT * from objectifs_specifiques");
        } else {
            // Via la table pivot structure_programme_budgetaire
            $objectifs = ObjectifSpecifique::whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->get();
        }
        $periodes = Periode::all();
        $activite = Activite::with('periodes')->findOrFail($ulid);

        // Récupérer les IDs des périodes associées et l'année
        $activitePeriodes = $activite->periodes->pluck('id')->toArray();
        $annee = $activite->periodes->first()->pivot->annee ?? date('Y');

        return view('activites.edit', compact('objectifs', 'activite', 'periodes', 'activitePeriodes', 'annee'));
    }

    public function update(UpdateActiviteRequest $request, Activite $activite)
    {
        $validated = $request->validated();

        // Mettre à jour l'activité
        $updated = $this->activiteRepository->update($activite, $validated);

        // Synchroniser les périodes
        if (isset($validated['periodes']) && !empty($validated['periodes'])) {
            $periodesData = [];
            foreach ($validated['periodes'] as $periodeId) {
                $periodesData[$periodeId] = ['annee' => $validated['annee']];
            }
            $activite->periodes()->sync($periodesData);
        } else {
            $activite->periodes()->detach();
        }

        return redirect()->route('activites.index')->with('success', "L'activité a été mise à jour");
    }

    public function show($id)
    {
        $activite = Activite::findOrFail($id);
        return view('activites.show', compact('activite'));
    }

    /* public function update(UpdateActiviteRequest $request, Activite $activite)
    {
        $updated = $this->activiteRepository->update($activite, $request->validated());
        return redirect()->route('activites.index')->with('success', "L'activité a été mise à jour");
    } */

    public function destroy(Activite $activite)
    {
        $this->activiteRepository->delete($activite);
        return redirect()->back()->with("success", "L'activé a été supprimée.");
    }
}
