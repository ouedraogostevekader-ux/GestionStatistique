<?php


namespace App\Http\Controllers;

use App\Models\Tableau;
use App\Models\Categorie;
use App\Models\LigneTableau;
use App\Models\ColonneTableau;
use App\Models\Cellule;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Assignation;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TableauController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:tableau-list', only: ['index', 'show']),
            new Middleware('permission:tableau-create', only: ['create', 'store']),
            new Middleware('permission:tableau-edit', only: ['edit', 'update']),
            new Middleware('permission:tableau-delete', only: ['destroy']),

            new Middleware('permission:addLigne-create', only: ['addLigne']),
            new Middleware('permission:addColonne-create', only: ['addColonne']),

            new Middleware('permission:assignation-create', only: ['affecterStructures', 'storeAffectation']),
            new Middleware('permission:soumission-create', only: ['saisirDonnees', 'saveDonnees']),
        ];
    }

    public function index()
    {
        $structureId = auth()->user()->structure_id;
        if (auth()->user()->structure->sigle == 'DGESS') {
            $tableaux = Tableau::with('categorie')->get();
        } else {
            $tableaux = Tableau::whereIn('id', function ($query) use ($structureId) {
                $query->select('tableau_id')
                    ->from('assignations')
                    ->where('structure_id', $structureId);
            })->get();
        }

        return view('tableaux.index', compact('tableaux'));
    }

    public function create()
    {
        $categories = Categorie::all();
        return view('tableaux.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string|max:500',
            'categorie_id' => 'required|exists:categories,id'
        ]);

        $tableau = Tableau::create([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'categorie_id' => $request->categorie_id
        ]);

        return redirect()->route('tableaux.index')
            ->with('success', 'Tableau créé avec succès');
    }

    public function show($id)
    {
        // Récupère le tableau avec ses relations principales
        $tableau = Tableau::with(['categorie', 'lignesTableau', 'colonnesTableau'])->findOrFail($id);

        // Récupère les lignes principales avec leurs enfants
        $lignes = LigneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

        // Récupère les colonnes principales avec leurs enfants
        $colonnes = ColonneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

        // Récupère les assignations du tableau avec les structures associées
        $assignations = Assignation::where('tableau_id', $id)
            ->with('structure')
            ->get();

        // Extrait les structures assignées à partir des assignations
        $structures = $assignations->pluck('structure')->unique('id');

        // Récupère toutes les soumissions liées au tableau
        $soumissions = $this->getSoumissionsData($id);

        // Envoie les données à la vue
        return view('tableaux.show', compact(
            'tableau',
            'lignes',
            'colonnes',
            'assignations',
            'soumissions',
            'structures'
        ));
    }



    public function edit($id)
    {
        $tableau = Tableau::findOrFail($id);
        $categories = Categorie::all();

        $lignes = LigneTableau::where('tableau_id', $id)
            ->orderBy('ordre')
            ->get();

        $colonnes = ColonneTableau::where('tableau_id', $id)
            ->orderBy('ordre')
            ->get();

        return view('tableaux.edit', compact('tableau', 'categories', 'lignes', 'colonnes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'required|string|max:250',
            'description' => 'nullable|string|max:250',
            'categorie_id' => 'required|exists:categories,id'
        ]);

        $tableau = Tableau::findOrFail($id);
        $tableau->update($request->only(['libelle', 'description', 'categorie_id']));

        return redirect()->route('tableaux.show', $id)
            ->with('success', 'Tableau mis à jour avec succès');
    }

    public function destroy($id)
    {
        $tableau = Tableau::findOrFail($id);
        $tableau->delete();

        return redirect()->route('tableaux.index')
            ->with('success', 'Tableau supprimé avec succès');
    }

    public function structureTableau($id)
    {
        $tableau = Tableau::with('categorie')->findOrFail($id);

        $lignes = LigneTableau::where('tableau_id', $id)
            ->orderBy('ordre')
            ->get();

        $colonnes = ColonneTableau::where('tableau_id', $id)
            ->orderBy('ordre')
            ->get();

        return view('tableaux.structure', compact('tableau', 'lignes', 'colonnes'));
    }

    public function addLigne(Request $request, $id)
    {
        $request->validate([
            'nom_ligne' => 'required|string|max:250',
            'ordre' => 'required|numeric',
            'parent_id' => 'nullable|exists:lignes_tableau,id'
        ]);

        $ligne = LigneTableau::create([
            'id' => Str::ulid(),
            'nom_ligne' => $request->nom_ligne,
            'ordre' => $request->ordre,
            'tableau_id' => $id,
            'parent_id' => $request->parent_id
        ]);

        // Créer les cellules pour cette nouvelle ligne
        $this->createCellulesForNewLigne($ligne);

        return redirect()->back()->with('success', 'Ligne ajoutée avec succès');
    }

    public function addColonne(Request $request, $id)
    {
        $request->validate([
            'colonne_ligne' => 'required|string|max:250',
            'ordre' => 'required|numeric',
            'parent_id' => 'nullable|exists:colonnes_tableau,id'
        ]);

        $colonne = ColonneTableau::create([
            'id' => Str::ulid(),
            'colonne_ligne' => $request->colonne_ligne,
            'ordre' => $request->ordre,
            'tableau_id' => $id,
            'parent_id' => $request->parent_id
        ]);

        // Créer les cellules pour cette nouvelle colonne
        $this->createCellulesForNewColonne($colonne);

        return redirect()->back()->with('success', 'Colonne ajoutée avec succès');
    }

    public function formSoumission($assignation_id)
    {
        $assignation = Assignation::findOrFail($assignation_id);

        // Ancienne assignation
        $previous = $assignation->getPreviousAssignation();

        $oldValues = [];

        if ($previous) {
            $oldValues = SoumissionTableau::where('assignation_id', $previous->id)
                ->pluck('valeur', 'cellule_id')
                ->toArray();
        }

        $cellules = Cellule::where('tableau_id', $assignation->tableau_id)->get();

        return view('soumissions.form', [
            'assignation' => $assignation,
            'cellules' => $cellules,
            'oldValues' => $oldValues,
        ]);
    }

    public function saisirDonnees($id)
    {
        $tableau = Tableau::with('categorie')->findOrFail($id);

        // Structure liée à l'utilisateur connecté
        $structure = auth()->user()->structure;

        if (!$structure) {
            return redirect()->route('tableaux.index')
                ->with('error', 'Aucune structure associée à votre compte.');
        }

        // Vérifie que le tableau est bien assigné à cette structure
        $assignations = Assignation::where('tableau_id', $id)
            ->where('structure_id', $structure->id)
            ->orderBy('version_id', 'desc')
            ->get();

        if ($assignations->isEmpty()) {
            return redirect()->route('tableaux.index')
                ->with('error', 'Ce tableau ne vous a pas été assigné. Vous n’êtes pas autorisé à saisir des données.');
        }

        // Dernière assignation (version actuelle)
        $latestAssignation = $assignations->first();

        // Version précédente
        $previousAssignation = $latestAssignation->getPreviousAssignation();

        // Valeurs précédentes (si une version précédente existe)
        $oldValues = [];

        if ($previousAssignation) {
            $oldValues = SoumissionTableau::where('assignation_id', $previousAssignation->id)
                ->pluck('valeur', 'cellule_id')
                ->toArray();
        }

        // Récupère les lignes et colonnes du tableau
        $lignes = LigneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

        $colonnes = ColonneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

        // Pour affichage (utile uniquement si tu veux afficher les anciennes soumissions)
        $soumissions = $this->getSoumissionsData($id);

        return view('tableaux.saisie', compact(
            'tableau',
            'structure',
            'assignations',
            'lignes',
            'colonnes',
            'soumissions',
            'latestAssignation',
            'previousAssignation',
            'oldValues'
        ));
    }


    public function saveDonnees(Request $request, $id)
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id',
            'assignation_id' => 'required|exists:assignations,id',
            'donnees' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->donnees as $cellule_id => $valeur) {
                if (!empty($valeur)) {
                    SoumissionTableau::updateOrCreate(
                        [
                            'tableau_id' => $id,
                            'structure_id' => $request->structure_id,
                            'assignation_id' => $request->assignation_id,
                            'cellule_id' => $cellule_id
                        ],
                        [
                            'id' => Str::ulid(),
                            'valeur' => $valeur
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Données sauvegardées avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de la sauvegarde');
        }
    }

    private function createCellulesForNewLigne($ligne)
    {
        $colonnes = ColonneTableau::where('tableau_id', $ligne->tableau_id)->get();

        foreach ($colonnes as $colonne) {
            Cellule::create([
                'id' => Str::ulid(),
                'ligne_id' => $ligne->id,
                'colonne_id' => $colonne->id
            ]);
        }
    }

    private function createCellulesForNewColonne($colonne)
    {
        $lignes = LigneTableau::where('tableau_id', $colonne->tableau_id)->get();

        foreach ($lignes as $ligne) {
            Cellule::create([
                'id' => Str::ulid(),
                'ligne_id' => $ligne->id,
                'colonne_id' => $colonne->id
            ]);
        }
    }

    public function getSoumissionsData($tableau_id)
    {
        return SoumissionTableau::where('tableau_id', $tableau_id)
            ->with(['cellule', 'structure', 'assignation'])
            ->get()
            ->groupBy(function ($item) {
                return $item->structure_id . '_' . $item->assignation_id;
            });
    }


    public function affecterStructures($id)
    {
        $tableau = Tableau::findOrFail($id);
        $structures = Structure::all();
        $versions = Version::all();
        return view('tableaux.assignations.affecter', compact('tableau', 'structures', 'versions'));
    }

    public function storeAffectation(Request $request, $id)
    {
        $request->validate([
            'structure_ids' => 'required|array',
            'structure_ids.*' => 'exists:structures,id',
            'version' => 'required|string|exists:versions,id',
            // 'periode' => 'nullable|string|max:250',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ], [
            'version.required' => 'La version est obligatoire, veuillez donc la choisir.',
            'structure_ids.required' => 'La structure est obligatoire, veuillez donc choisir au moins une structure.',
            'date_debut.required' => 'La date de début est obligatoire, veuillez donc la renseigner.',
            'date_fin.required' => 'La date de fin est obligatoire, veuillez donc la renseigner.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être supérieur à la date début.',
        ]);
        $versionId = $request->version;
        $structureIds = $request->structure_ids;

        // Vérification personnalisée : combinaison unique
        $dejaAssignes = Assignation::where('tableau_id', $id)
            ->where('version_id', $versionId)
            ->whereIn('structure_id', $structureIds)
            ->pluck('structure_id')
            ->toArray();

        if (!empty($dejaAssignes)) {
            // Récupérer les noms des structures déjà assignées
            $noms = Structure::whereIn('id', $dejaAssignes)
                ->pluck('sigle')
                ->implode(', ');

            return back()
                ->withInput()
                ->withErrors([
                    'structure_ids' => "La version sélectionnée est déjà assignée aux structures suivantes : $noms.",
                ]);
        }

        foreach ($request->structure_ids as $structureId) {
            Assignation::create([
                'id' => Str::ulid(),
                'tableau_id' => $id,
                'structure_id' => $structureId,
                'version_id' => $request->version,
                // 'periode' => $request->periode,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'annee' => now()->year,
                'statut' => Assignation::determinerStatut($request->date_debut, $request->date_fin),
            ]);
        }

        return redirect()->route('tableaux.show', $id)->with('success', 'Structures assignées avec succès');
    }
}
