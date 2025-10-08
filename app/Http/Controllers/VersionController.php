<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VersionController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:version-list', only: ['index', 'show']),
            new Middleware('permission:version-create', only: ['create', 'store']),
            new Middleware('permission:version-edit', only: ['edit', 'update']),
            new Middleware('permission:version-delete', only: ['destroy']),
        ];
    }

    /**
     * Afficher la liste des versions
     */
    public function index()
    {
        $versions = Version::all();
        return view('versions.index', compact('versions'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('versions.create');
    }

    /**
     * Enregistrer une nouvelle version
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:versions,code',
            'libelle' => 'nullable|string|max:255',
            'annee' => 'nullable|string|max:10',
        ]);

        Version::create($request->all());

        return redirect()->route('versions.index')->with('success', 'Version créée avec succès.');
    }

    /**
     * Afficher les détails d'une version
     */
    public function show($id)
    {
        $version = Version::findOrFail($id);
        return view('versions.show', compact('version'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $version = Version::findOrFail($id);
        return view('versions.edit', compact('version'));
    }

    /**
     * Mettre à jour une version
     */
    public function update(Request $request, $id)
    {
        $version = Version::findOrFail($id);
        $request->validate([
            'code' => 'required|unique:versions,code,' . $version->id,
            'libelle' => 'nullable|string|max:255',
            'annee' => 'nullable|string|max:10',
        ]);

        $version->update($request->all());

        return redirect()->route('versions.index')->with('success', 'Version mise à jour avec succès.');
    }

    /**
     * Supprimer une version
     */
    public function destroy($id)
    {
        $version = Version::findOrFail($id);
        $version->delete();
        return redirect()->route('versions.index')->with('success', 'Version supprimée avec succès.');
    }
}
