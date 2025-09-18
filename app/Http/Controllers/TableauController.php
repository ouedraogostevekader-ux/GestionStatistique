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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TableauController extends Controller
{
    public function index()
    {
        $tableaux = Tableau::with('categorie')->paginate(10);
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
            'libelle' => 'required|string|max:250',
            'description' => 'nullable|string|max:250',
            'categorie_id' => 'required|exists:categories,id'
        ]);

        $tableau = Tableau::create([
            'id' => Str::ulid(),
            'libelle' => $request->libelle,
            'description' => $request->description,
            'categorie_id' => $request->categorie_id
        ]);

        return redirect()->route('tableaux.show', $tableau->id)
            ->with('success', 'Tableau créé avec succès');
    }

    public function show($id)
    {
        $tableau = Tableau::with(['categorie', 'lignesTableau', 'colonnesTableau'])->findOrFail($id);

        // Récupérer les lignes principales (sans parent)
        $lignes = LigneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

        // Récupérer les colonnes principales (sans parent)
        $colonnes = ColonneTableau::where('tableau_id', $id)
            ->whereNull('parent_id')
            ->orderBy('ordre')
            ->with('enfants')
            ->get();

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

    $assignations = Assignation::where('tableau_id', $id)
                               ->with('structure')
                               ->get();

    $soumissions = $this->getSoumissionsData($id);

    return view('tableaux.show', compact('tableau', 'lignes', 'colonnes', 'assignations', 'soumissions'));
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

    public function saisirDonnees($id)
    {
        $tableau = Tableau::with('categorie')->findOrFail($id);
        $structures = Structure::all();
        $assignations = Assignation::where('tableau_id', $id)->get();

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

        $soumissions = $this->getSoumissionsData($id);

        return view('tableaux.saisie', compact('tableau', 'structures', 'assignations', 'lignes', 'colonnes', 'soumissions'));
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

    private function getSoumissionsData($tableau_id)
    {
        return SoumissionTableau::where('tableau_id', $tableau_id)
            ->with(['cellule.ligne', 'cellule.colonne', 'structure'])
            ->get()
            ->keyBy(function ($item) {
                return $item->cellule_id . '_' . $item->structure_id;
            });
    }

    public function affecterStructures($id)
{
    $tableau = Tableau::findOrFail($id);
    $structures = Structure::all();

    return view('tableaux.affecter', compact('tableau', 'structures'));
}

public function storeAffectation(Request $request, $id)
{
    $request->validate([
        'structure_ids' => 'required|array',
        'structure_ids.*' => 'exists:structures,id',
        'version' => 'nullable|string|max:250',
        'periode' => 'nullable|string|max:250',
        'date_debut' => 'nullable|date',
        'date_fin' => 'nullable|date',
    ]);

    foreach ($request->structure_ids as $structureId) {
        Assignation::create([
            'id' => Str::ulid(),
            'tableau_id' => $id,
            'structure_id' => $structureId,
            'version' => $request->version,
            'periode' => $request->periode,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);
    }

    return redirect()->route('tableaux.show', $id)->with('success', 'Structures assignées avec succès');
}

}