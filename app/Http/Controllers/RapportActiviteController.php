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
use App\Models\Periode;
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
            new Middleware('permission:rapport_Activite-list', only: ['index', 'show']),
            new Middleware('permission:rapport_Activite-create', only: ['create', 'store']),
            new Middleware('permission:rapport_Activite-edit', only: ['edit', 'update']),
            new Middleware('permission:rapport_Activite-delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $rapportActivites = RapportActivite::all();
        $programmeActivites = ProgrammesActivite::where('annee', now()->year)->get();
        //$programmeActivites = DB::select("SELECT * from programmes_activites");
        $periodes = Periode::pluck('libelle', 'id');
        return view('rapportActivites.index', compact('rapportActivites', 'programmeActivites', 'periodes'));
    }

    public function create()
    {
        $programmeActivite = ProgrammesActivite::where('annee', now()->year)->latest()->first();
        $periodes = Periode::pluck('libelle', 'id');
        return view('rapportActivites.create', compact('programmeActivite', 'periodes'));
    }

    public function store(RapportActiviteRequest $request)
    {
        // Pour année en cours empecher nouvelle génération

        DB::beginTransaction();
        try {

            //$programmeActivite = ProgrammesActivite::findOrFail($request->programme_activite_id);
            $programmeActivite = ProgrammesActivite::where('annee', now()->year)->orderBy('id', 'desc')->first();
            if (!$programmeActivite) {
                
                if ($request->ajax()) {
                    throw new \Exception('Aucun programme d\'activité trouvé pour l\'annee en cours ' . now()->year);
                    return response()->json([
                        'success' => false,
                        'message' => 'Aucun programme d\'activité trouvé pour l\'année en cours ' . now()->year
                    ]);
                }
                return redirect()->back()->with('error', 'Aucun programme d\'activité trouvé pour l\'année en cours ' . now()->year);
            }
            $rapport = RapportActivite::where('annee', now()->year)->count();
            if ($rapport > 2) {
                
                if ($request->ajax()) {
                    throw new \Exception('Deux (02) rapports d\'activité pour l\'annee en cours ' . now()->year . ' ont deja ete genere.');
                    return response()->json([
                        'success' => false,
                        'message' => 'Deux (02) rapports d\'activité pour l\'année en cours ' . now()->year . ' ont été déjà générés.'
                    ]);
                }
                return redirect()->back()->with('error', 'Deux (02) rapports d\'activité pour l\'année en cours ' . now()->year . ' ont été déjà générés.');
            }
            $periode = $request->input('periode_rapport');
            $annee = now()->year;
            $date = now();
            $data = $programmeActivite->programmesBudgetaires;

            // Generation du fichier excel
            $excel = new RapportActiviteExport($data, $periode, $annee, $date);
            $url_excel = (string) Str::uuid();
            Excel::store($excel, 'rapports/' . $url_excel . '.xlsx', 'public');

            // Generation du fichier PDF
            $pdf = PDF::loadView('rapportActivites.pdf', compact('data', 'periode', 'annee', 'date'));
            $pdf->setPaper('a4', 'landscape');
            $url_pdf = (string) Str::uuid();
            $pdf->save(storage_path('app/public/rapports/' . $url_pdf . '.pdf'));

            $input = $request->validated();
            $input['programme_activite_id'] = $programmeActivite->id;
            $input['annee'] = $annee;
            $input['date_rapport'] = $date;
            $input['file'] = $url_excel . '.xlsx';
            $input['file_pdf'] = $url_pdf . '.pdf';
            $rapport = RapportActivite::create($input);
            DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rapport d\'activité créé avec succès!'
                ]);
            }
            return redirect()->route('rapportActivites.index')->with('success', 'Rapport d\'activité créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->with('error', 'Une erreur est survenue' . $e->getMessage());
        }
    }

    public function visualiser(Request $request)
    {
        $periode = $request->input('periode_rapport');
        $annee = $request->input('annee');

        // Si aucun programme n'est sélectionné, rediriger avec un message d'erreur
        if (!$request->has('programmes') || empty($request->input('programmes'))) {
            if ($request->ajax()) {
                throw new \Exception('Veuillez sélectionner au moins un programme budgétaire');
                return response()->json([
                    'success' => false,
                    'message' => 'Veuillez sélectionner au moins un programme budgétaire'
                ]);
            }
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins un programme budgétaire.');
        }

        $data = ProgrammeBudgetaire::whereIn('id', $request->input('programmes'))->get();

        return view('rapportActivites.visualiser', compact('data', 'periode', 'annee'));
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
