<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgrammesActivite;
use App\Models\ProgrammeBudgetaire;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProgrammeActiviteController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:programme_Activite-list', only: ['index', 'show']),
            new Middleware('permission:programme_Activite-create', only: ['create', 'store']),
            new Middleware('permission:programme_Activite-edit', only: ['edit', 'update']),
            new Middleware('permission:programme_Activite-delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programmes = ProgrammeBudgetaire::with([
            'objectifsStrategiques' => function ($query) {
                $query->with([
                    'actions' => function ($query) {
                        $query->with([
                            'objectifsSpecifiques' => function ($query) {
                                $query->with([
                                    'activites' => function ($query) {
                                        $query->with(['indicateurs', 'periodes']);
                                    },
                                    'indicateurs'
                                ]);
                            }
                        ]);
                    },
                    'indicateurs'
                ]);
            },
            'programmeActivite',
            'structures'
        ])->get();


        return view('prog_activite.index', compact('programmes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $programme = ProgrammeBudgetaire::with([
            'objectifsStrategiques.actions.objectifsSpecifiques.activites.indicateurs.suivis',
            'objectifsStrategiques.actions.objectifsSpecifiques.indicateurs.suivis',
            'objectifsStrategiques.indicateurs.suivis',
            'objectifsStrategiques.actions.objectifsSpecifiques.activites.periodes',
            'programmeActivite',
            'structures'
        ])->findOrFail($id);

        return view('prog_activite.show', compact('programme'));
    }

    public function tableau($id)
    {
        $programme = ProgrammeBudgetaire::with([
            'objectifsStrategiques' => function ($query) {
                $query->with([
                    'indicateurs',
                    'actions' => function ($query) {
                        $query->with([
                            'objectifsSpecifiques' => function ($query) {
                                $query->with([
                                    'indicateurs',
                                    'activites' => function ($query) {
                                        $query->with(['indicateurs', 'periodes']);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            },
            'programmeActivite'
        ])->findOrFail($id);

        return view('prog_activite.tableau', compact('programme'));
    }

    public function suiviTableau($id)
    {
        $programme = ProgrammeBudgetaire::with([
            'objectifsStrategiques.indicateurs.suivis',
            'objectifsStrategiques.actions.objectifsSpecifiques.indicateurs.suivis',
            'objectifsStrategiques.actions.objectifsSpecifiques.activites.indicateurs.suivis',
            'objectifsStrategiques.actions.objectifsSpecifiques.activites.periodes'
        ])->findOrFail($id);

        return view('prog_activite.suivi', compact('programme'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $programmeActivite = ProgrammesActivite::findOrFail($id);
        $programmeActivite->delete();
        return redirect()->route('programme_activites.index')->with('success', 'Programme d\'activité supprimé avec succès');
    }
}
