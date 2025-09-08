<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgrammesActivite;
use App\Models\ProgrammeBudgetaire;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\ProgrammeTableauExport;
use Carbon\Carbon;

class ProgrammeActiviteController extends Controller implements HasMiddleware
{
    public function __construct()
    {
    }

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
    // Liste des programmes d'activités
    public function listeProgrammeActivite()
    {

        $programmesActivites = ProgrammesActivite::orderBy('date_generation', 'desc')->get();
        return view('prog_activite.liste', compact('programmesActivites'));
    }
    // Afficher la liste des programmes budgétaires avec leurs relations
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
        ])
            ->where('annee', now()->year)
            ->get();


        return view('prog_activite.index', compact('programmes'));
    }
    // Afficher le détail d'un programme d'activité sous forme de tableau
    public function tableProgrammeActivite()
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


        return view('prog_activite.tableau', compact('programmes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prog_activite.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // Générer un nouveau programme d'activité
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'programmes' => 'required|array',
            'programmes.*' => 'exists:programmes_budgetaires,id' // Corrigé ici
        ]);
        DB::beginTransaction();
        try {
            // Vérifier que tous les programmes sont validés (statut > 0)
            $programmesNonValides = ProgrammeBudgetaire::whereIn('id', $request->programmes)
                ->where('statut', '<=', 0)
                ->get();

            if ($programmesNonValides->isNotEmpty()) {
                $nomsProgrammes = $programmesNonValides->pluck('libelle')->implode(', ');
                return redirect()->back()
                    ->with('error', 'Certains programmes ne sont pas validés : ' . $nomsProgrammes)
                    ->withInput();
            }
            // Récupérer les programmes sélectionnés
            $programmes = ProgrammeBudgetaire::with([
                'objectifsStrategiques.indicateurs',
                'objectifsStrategiques.actions.objectifsSpecifiques.indicateurs',
                'objectifsStrategiques.actions.objectifsSpecifiques.activites.indicateurs',
                'objectifsStrategiques.actions.objectifsSpecifiques.activites.periodes'
            ])->whereIn('id', $request->programmes)->get();

            // Créer le répertoire pour les fichiers s'il n'existe pas
            $directory = storage_path('app/public/programme-activites');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            // Générer les noms de fichiers uniques
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $cleanLibelle = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $request->libelle);

            $excelFilename = $cleanLibelle . '_' . $timestamp . '.xlsx';
            $pdfFilename = $cleanLibelle . '_' . $timestamp . '.pdf';

            $excelPath = $directory . '/' . $excelFilename;
            $pdfPath = $directory . '/' . $pdfFilename;

            // Générer le fichier Excel
            Excel::store(new ProgrammeTableauExport($programmes), 'programme-activites/' . $excelFilename, 'public');

            // Générer le fichier PDF
            $pdf = PDF::loadView('prog_activite.export-pdf', compact('programmes'))
                ->setPaper('a4', 'landscape');
            $pdf->save($pdfPath);
            // Enregistrer en base de données
            $programmeActivite = ProgrammesActivite::create([
                'libelle' => $request->libelle,
                'annee' => Carbon::now()->year,
                'file_pdf' => 'programme-activites/' . $pdfFilename,
                'file_excel' => 'programme-activites/' . $excelFilename,
                'date_generation' => Carbon::now(),
            ]);

            // Attacher les programmes à ce programme d'activité
            $programmeActivite->programmesBudgetaires()->attach($request->programmes);
            // dd($programmeActivite->programmesBudgetaires);
            // Verrouiller les programmes (isActive = false)
            ProgrammeBudgetaire::whereIn('id', $request->programmes)->update(['isActive' => false]);
            // Attacher les programmes à ce programme d'activité
            //$programmeActivite->programmesBudgetaires()->attach($request->programmes);
            DB::commit();
            return redirect()->route('programme-activites.liste')->with('success', 'Programme d\'activité généré avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    // Afficher le détail d'un programme budgétaire sous forme de tableau
    public function show($id)
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

        return view('prog_activite.show', compact('programme'));
    }

    //

    //Exporter les programmes d'activités en Excel
    public function exportExcel()
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
        ])
            ->where('annee', now()->year)
            ->get();
        return Excel::download(new ProgrammeTableauExport($programmes), 'programmes-tableau.xlsx');
    }

    //Exporter les programmes d'activités en PDF
    public function exportPDF()
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
        ])
            ->where('annee', now()->year)
            ->get();
        $pdf = PDF::loadView('prog_activite.export-pdf', compact('programmes'));
        return $pdf->download('programmes-tableau.pdf');
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
