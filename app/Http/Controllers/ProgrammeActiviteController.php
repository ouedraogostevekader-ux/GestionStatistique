<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgrammesActivite;
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
        //Liste des programmes d'activités paginées
        $programmeActivites = ProgrammesActivite::paginate(10);
        return view('programme_activites.index', compact('programmeActivites'));
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
    public function show(string $id)
    {
        //
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
