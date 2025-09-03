<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateActiviteRequest;
use App\Models\Activite;
use App\Repositories\ActiviteRepository;
use DB;

class ActiviteController extends Controller
{
    protected $activiteRepository;

    public function __construct(ActiviteRepository $activiteRepository)
    {
        $this->activiteRepository = $activiteRepository;
    }

    public function index()
    {
        $objectifs = DB::select("SELECT * from objectifs_specifiques");
        $activites = $this->activiteRepository->all();
        return view('activites.index', compact('activites', 'objectifs'));
    }

    /* Create Activite */
    public function create()
    {
        $objectifs = DB::select("SELECT * from objectifs_specifiques");
        return view('activites.create', compact('objectifs'));
    }

    public function store(StoreActiviteRequest $request)
    {
        $activite = $this->activiteRepository->create($request->validated());
        return redirect()->route('activites.index')->with('success', "L'activité " . $activite->libelle . "a été enregistrée.", );
    }

    /* Create Activite */
    public function edit($ulid)
    {
        $objectifs = DB::select("SELECT * from objectifs_specifiques");
        $activite = Activite::findOrFail($ulid);
        return view('activites.edit', compact('objectifs', 'activite'));
    }

    public function show($id)
    {
        $activite = Activite::findOrFail($id);
        return view('activites.show', compact('activite'));
    }

    public function update(UpdateActiviteRequest $request, Activite $activite)
    {
        $updated = $this->activiteRepository->update($activite, $request->validated());
        return redirect()->route('activites.index')->with('success', "L'activité a été mise à jour");
    }

    public function destroy(Activite $activite)
    {
        $this->activiteRepository->delete($activite);
        return redirect()->back()->with("success", "L'activé a été supprimée.");
    }
}
