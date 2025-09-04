<?php

namespace App\Http\Controllers;

use App\Models\RapportActivite;
use App\Models\ProgrammeActivite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RapportActiviteController extends Controller implements HasMiddleware
{
    //

    public static function middleware(): array
    {
        return [
            new Middleware('permission:rapportActivite-list', only: ['index', 'show']),
            new Middleware('permission:rapportActivite-create', only: ['create', 'store']),
            new Middleware('permission:rapportActivite-edit', only: ['edit', 'update']),
            new Middleware('permission:rapportActivite-delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $rapportActivites = RapportActivite::all();
        //$programmeActivites = ProgrammeActivite::all();
        $programmeActivites = DB::select("SELECT * from programmes_activites");
        $periodes = ['T1'=>'T1', 'T2'=>'T2', 'T3'=>'T3', 'T4'=>'T4'];
        return view('rapportActivites.index', compact('rapportActivites', 'programmeActivites', 'periodes'));
    }
}
