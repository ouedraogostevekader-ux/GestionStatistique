<?php

namespace App\Http\Controllers;

use App\Models\Assignation;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\SoumissionTableau;
use App\Models\Tableau;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AnnuaireController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:annuaire-list', only: ['index', 'show']),
            new Middleware('permission:annuaire-create', only: ['create', 'store']),
            new Middleware('permission:annuaire-edit', only: ['edit', 'update']),
            new Middleware('permission:annuaire-delete', only: ['destroy']),

            new Middleware('permission:annuaire-exportWord', only: ['exportWord']),
            new Middleware('permission:annuaire-exportPdf', only: ['exportPdf']),
        ];
    }

    public function index(Request $request)
    {
        $version = $request->input('version');
        $assignations = Assignation::where('version_id', $version)->pluck('id');
        $soumissions = SoumissionTableau::whereIn('assignation_id', $assignations)->get();
        $versions = Version::all();
        $tableaux = Tableau::whereIn('id', function ($query) use ($version) {
            $query->select('tableau_id')
                ->from('soumissions_tableau')
                ->whereIn('assignation_id', function ($subQuery) use ($version) {
                    $subQuery->select('id')
                        ->from('assignations')
                        ->where('version_id', $version);
                });
        })->with(['lignesTableau', 'colonnesTableau'])->get();

        $cellules = Cellule::all();

        return view('annuaires.index', compact('versions', 'version', 'tableaux', 'cellules', 'assignations', 'soumissions'));
    }

    public function exportWord() {}

    public function exportPdf() {}
}
