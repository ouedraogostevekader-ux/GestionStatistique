<?php

namespace App\Http\Controllers;

use App\Http\Requests\RapportActiviteRequest;
use App\Models\RapportActivite;
use App\Models\ProgrammeActivite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RapportActiviteExport;
use App\Models\ProgrammeBudgetaire;
use App\Models\ProgrammesActivite;
use PDF;
use Illuminate\Support\Str;

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

    public function create()
    {
        //$programmeActivites = ProgrammeActivite::all();
        $programmeActivites = DB::select("SELECT * from programmes_activites");;
        $periodes = ['T1'=>'T1', 'T2'=>'T2', 'T3'=>'T3', 'T4'=>'T4'];
        return view('rapportActivites.create', compact('programmeActivites', 'periodes'));
    }

    public function store(RapportActiviteRequest $request)
    {
        
        //DB::beginTransaction();
        //try {
        
            $programmeActivite = ProgrammesActivite::findOrFail($request->programme_activite_id);
            $periode = $request->input('periode_rapport');
            $annee = $request->input('annee');
            $date = $request->date('date_rapport');
            $data = $programmeActivite->programmesBudgetaires;

            // Generation du fichier excel
            $excel = new RapportActiviteExport($data, $periode, $annee, $date);
            $url_excel = (string) Str::uuid();
            Excel::store($excel, 'rapports/'.$url_excel.'.xlsx', 'public');

            // Generation du fichier PDF
            $pdf = PDF::loadView('rapportActivites.pdf', compact('data', 'periode', 'annee', 'date'));
            $pdf->setPaper('a4', 'landscape');
            $url_pdf = (string) Str::uuid();
            $pdf->save(storage_path('app/public/rapports/'.$url_pdf.'.pdf'));
            
            $input = $request->validated();
            $input['file'] = $url_excel.'.xlsx';
            $input['file_pdf'] = $url_pdf.'.pdf';
            $rapport = RapportActivite::create($input);
            //DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rapport d\'activité créé avec succès!'
                ]);
            }
            return redirect()->route('rapportActivites.index')->with('success', 'Rapport d\'activité créé avec succès');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Une erreur est survenue: '.$e->getMessage()
        //         ]);
        //     }
        //     return redirect()->back()->with('error', 'Une erreur est survenue'.$e->getMessage());
        // }
    }

    public function visualiser(Request $request)
    {
        
        //DB::beginTransaction();
        //try {
            $periode = $request->input('periode_rapport');
            $annee = $request->input('annee');

            $data = ProgrammeBudgetaire::where('annee', $request->input('annee'))->get();

            // Generation du fichier excel
            // $excel = new RapportActiviteExport($data, $periode, $annee);
            // $url_excel = (string) Str::uuid();
            // Excel::store($excel, 'rapports/'.$url_excel.'.xlsx', 'public');

            // Generation du fichier PDF
            // $pdf = PDF::loadView('rapportActivites.pdf', compact('data', 'data', 'periode', 'annee'));
            // $pdf->setPaper('a4', 'landscape');
            // $url_pdf = (string) Str::uuid();
            // $pdf->save(storage_path('app/public/rapports/'.$url_pdf.'.pdf'));
            
            // $input = $request->validated();
            // $input['file'] = $url_excel.'.xlsx';
            // $input['file_pdf'] = $url_pdf.'.pdf';
            // $rapport = RapportActivite::create($input);
            //DB::commit();
            // if ($request->ajax()) {
            //     return response()->json([
            //         'success' => true,
            //         'message' => 'Rapport d\'activité créé avec succès!'
            //     ]);
            // }
            return redirect()->route('rapportActivites.visualiser', compact('data', 'periode', 'annee'));
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Une erreur est survenue: '.$e->getMessage()
        //         ]);
        //     }
        //     return redirect()->back()->with('error', 'Une erreur est survenue'.$e->getMessage());
        // }
    }

    public function destroy(Request $request, $id)
    {
        //
        $rapport = RapportActivite::findOrFail($id);
        $rapport->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Rapport d\'activité supprimé avec succès!'
            ]);
        }
        return redirect()->route('rapportActivites.index');
    }
}
