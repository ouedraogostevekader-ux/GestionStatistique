<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteIndicateurRequest;
use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateActiviteIndicateurRequest;
use App\Http\Requests\UpdateActiviteRequest;
use App\Models\Activite;
use App\Models\IndicateurProgramme;
use App\Models\ObjectifSpecifique;
use App\Models\Periode;
use App\Models\ProgrammeBudgetaire;
use App\Models\Structure;
use App\Repositories\ActiviteRepository;
use DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActiviteController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:activite-list', only: ['index', 'show', 'indexActivitesHorsProgramme']),
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

    public function index(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $trimestre = $request->get('trimestre');
        $objectifSpecifiqueId = $request->get('objectif_specifique_id');
        $programmeBudgetaireId = $request->get('programme_budgetaire_id');
        $structureId = $request->get('structure_id');

        // Initialiser la variable structures dès le début
        $structures = collect();

        // Base query selon les permissions de l'utilisateur
        if (Auth::user()->isAdmin()) {
            $activitesQuery = Activite::with([
                'objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures',
                'periodes',
                'objectifSpecifique.indicateursProgrammes'
            ]);

            $objectifs = ObjectifSpecifique::with('action.objectifStrategique.programmeBudgetaire')
                ->orderBy('libelle')
                ->get();

            $programmes = ProgrammeBudgetaire::orderBy('libelle')->get();

            // Pour les admins, récupérer toutes les structures
            $structures = Structure::orderBy('nom')->get();

        } else {
            $activitesQuery = Activite::with([
                'objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures',
                'periodes',
                'objectifSpecifique.indicateursProgrammes'
            ])->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            });

            $objectifs = ObjectifSpecifique::with('action.objectifStrategique.programmeBudgetaire')
                ->whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                    $query->where('structure_id', Auth::user()->structure_id);
                })
                ->orderBy('libelle')
                ->get();

            $programmes = ProgrammeBudgetaire::whereHas('structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->orderBy('libelle')
                ->get();

            // Pour les non-admin, récupérer seulement leur structure
            $structures = Structure::where('id', Auth::user()->structure_id)->get();
        }

        // Filtrage par année
        if ($annee) {
            $activitesQuery->where('annee', $annee);
        }

        // Filtrage par trimestre
        if ($trimestre) {
            $periodeName = 'T' . $trimestre;
            $activitesQuery->whereHas('periodes', function ($q) use ($periodeName) {
                $q->where('libelle', $periodeName);
            });
        }

        // Filtrage par objectif spécifique
        if ($objectifSpecifiqueId) {
            $activitesQuery->where('objectif_specifique_id', $objectifSpecifiqueId);
        }

        // Filtrage par programme budgétaire
        if ($programmeBudgetaireId) {
            $activitesQuery->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire', function ($q) use ($programmeBudgetaireId) {
                $q->where('id', $programmeBudgetaireId);
            });
        }

        // Filtrage par structure (nouveau)
        if ($structureId) {
            $activitesQuery->whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($q) use ($structureId) {
                $q->where('structure_id', $structureId);
            });
        }

        // Récupération des activités avec pagination
        $activites = $activitesQuery->orderBy('created_at', 'desc')->paginate(1000);

        // Données pour les filtres
        $periodes = collect([
            (object) ['id' => 1, 'libelle' => 'T1'],
            (object) ['id' => 2, 'libelle' => 'T2'],
            (object) ['id' => 3, 'libelle' => 'T3'],
            (object) ['id' => 4, 'libelle' => 'T4']
        ]);

        $annees = range(date('Y') - 2, date('Y') + 2);

        // Statistiques rapides
        $stats = [
            'total' => $activites->total(),
            'budget_total' => $activitesQuery->sum('cout_previsionnel'),
            'moyenne_cout' => $activitesQuery->avg('cout_previsionnel')
        ];

        return view('activites.index', compact(
            'activites',
            'objectifs',
            'programmes',
            'structures',
            'periodes',
            'annees',
            'stats',
            'annee',
            'trimestre',
            'objectifSpecifiqueId',
            'programmeBudgetaireId',
            'structureId'
        ));
    }
    /* Activité hors programmes */
    public function indexActivitesHorsProgramme()
    {
        if (Auth::user()->isAdmin()) {
            $objectifs = DB::select("SELECT * from objectifs_specifiques");
            $activites = Activite::onlyHorsProgramme()->get();
        } else {
            // Via la table pivot structure_programme_budgetaire
            $objectifs = ObjectifSpecifique::whereHas('action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->get();

            $activites = Activite::whereHas('objectifSpecifique.action.objectifStrategique.programmeBudgetaire.structures', function ($query) {
                $query->where('structure_id', Auth::user()->structure_id);
            })
                ->onlyHorsProgramme()->get();
        }
        return view('activites.index-hors-programme', compact('activites', 'objectifs'));
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

    /* Create Activite hors programme */
    public function createHorsProgramme()
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
        return view('activites.create-horsprogramme', compact('objectifs', 'periodes'));
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

    public function storeActiviteIndicateur(StoreActiviteIndicateurRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            //   dd($validated);
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

            // Créer les indicateurs associés
            if (isset($validated['indicateurs']) && !empty($validated['indicateurs'])) {
                foreach ($validated['indicateurs'] as $indicateurData) {
                    // Nettoyer les données vides
                    $indicateurData = array_filter($indicateurData, function ($value) {
                        return !is_null($value) && $value !== '';
                    });

                    if (!empty($indicateurData) && isset($indicateurData['libelle'])) {
                        IndicateurProgramme::create([
                            'libelle' => $indicateurData['libelle'],
                            'unite_mesure' => $indicateurData['unite_mesure'],
                            'valeur_ref' => $indicateurData['valeur_ref'],
                            'valeur_cible' => $indicateurData['valeur_cible'],
                            'annee' => $indicateurData['annee'] ?? $validated['annee'],
                            // 'objectif_specifique_id' => $validated['objectif_specifique_id'],
                            'activites_id' => $activite->id,
                            // Récupérer l'objectif stratégique via les relations
                            //'objectif_strategique_id' => $activite->objectifSpecifique->action->objectif_strategique_id ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->activite) {
                return redirect()->back()
                    ->with('success', 'Activité avec indicateurs créée avec succès.');
            }

            return redirect()->route('activites.index')
                ->with('success', "L'activité '{$activite->libelle}' avec ses indicateurs a été enregistrée avec succès.");

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }
    public function edit($ulid)
    {
        $activite = Activite::with('periodes')->findOrFail($ulid);

        if (!$activite->activiteIsEdit()) {
            return redirect()->back()->with('error', "Cette activité n\'est pas modifiable");
        }
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

        // Récupérer les IDs des périodes associées et l'année
        $activitePeriodes = $activite->periodes->pluck('id')->toArray();
        $annee = $activite->periodes->first()->pivot->annee ?? date('Y');

        return view('activites.edit', compact('objectifs', 'activite', 'periodes', 'activitePeriodes', 'annee'));
    }
    /* Update activité hors programme */
    public function editHorsProgramme($id)
    {
        //$activite = Activite::with('periodes')->findOrFail($ulid);
        $activite = Activite::with('periodes')->onlyHorsProgramme()->where('id', $id)->first();

        if (!$activite->activiteIsEdit()) {
            return redirect()->back()->with('error', "Cette activité n\'est pas modifiable");
        }
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

        // Récupérer les IDs des périodes associées et l'année
        $activitePeriodes = $activite->periodes->pluck('id')->toArray();
        $annee = $activite->periodes->first()->pivot->annee ?? date('Y');

        return view('activites.edit', compact('objectifs', 'activite', 'periodes', 'activitePeriodes', 'annee'));
    }

    public function update(UpdateActiviteRequest $request, $activite_id)
    {
        $validated = $request->validated();
        $activite = Activite::findOrFail($activite_id);
        if (!$activite->activiteIsEdit()) {
            return redirect()->back()->with('error', "Cette activité n\'est pas modifiable");
        }
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

    /* Update Activite with indicateur */
    public function updateActiviteIndicateur(UpdateActiviteIndicateurRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $activite = Activite::findOrFail($id);
            // Mettre à jour l'activité
            $activite->update($validated);

            // Mettre à jour les périodes
            if (isset($validated['periodes']) && !empty($validated['periodes'])) {
                // Détacher toutes les anciennes périodes
                $activite->periodes()->detach();

                // Attacher les nouvelles périodes
                $periodesData = [];
                foreach ($validated['periodes'] as $periodeId) {
                    $periodesData[$periodeId] = ['annee' => $validated['annee']];
                }
                $activite->periodes()->attach($periodesData);
            }

            // Mettre à jour les indicateurs
            if (isset($validated['indicateurs'])) {
                // Supprimer les anciens indicateurs liés à cette activité
                IndicateurProgramme::where('activites_id', $activite->id)->delete();

                // Créer les nouveaux indicateurs
                foreach ($validated['indicateurs'] as $indicateurData) {
                    // Nettoyer les données vides
                    $indicateurData = array_filter($indicateurData, function ($value) {
                        return !is_null($value) && $value !== '';
                    });

                    if (!empty($indicateurData) && isset($indicateurData['libelle'])) {
                        IndicateurProgramme::create([
                            'libelle' => $indicateurData['libelle'],
                            'unite_mesure' => $indicateurData['unite_mesure'],
                            'valeur_ref' => $indicateurData['valeur_ref'],
                            'valeur_cible' => $indicateurData['valeur_cible'],
                            'annee' => $indicateurData['annee'] ?? $validated['annee'],
                            'activites_id' => $activite->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('activites.index')
                ->with('success', "L'activité '{$activite->libelle}' avec ses indicateurs a été mise à jour avec succès.");

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $activite = Activite::findOrFail($id);
        //  dd($activite->activiteIsEdit());
        return view('activites.show', compact('activite'));
    }

    public function showHorsProgramme($id)
    {
        $activite = Activite::onlyHorsProgramme()->where('id', $id)->first();
        //dd($activite->activiteIsEdit());
        return view('activites.show-horsprogramme', compact('activite'));
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

    public function deleteHorsProgramme($id)
    {
        $activite = Activite::onlyHorsProgramme()->where('id', $id)->first();
        $activite->delete();
        return redirect()->back()->with("success", "L'activé a été supprimée.");
    }
}
