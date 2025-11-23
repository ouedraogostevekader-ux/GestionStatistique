<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Models\Tableau;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategorieController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:categorie-list', only: ['index', 'show']),
            new Middleware('permission:categorie-create', only: ['create', 'store']),
            new Middleware('permission:categorie-edit', only: ['edit', 'update']),
            new Middleware('permission:categorie-delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $categories = Categorie::withCount('tableaux')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:1000|unique:categories,libelle'
        ]);

        Categorie::create([
            // 'id' => Str::ulid(),
            'libelle' => $request->libelle
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès');
    }

    public function show($id)
    {
        $categorie = Categorie::with('tableaux')->findOrFail($id);
        $tableauxExistants = Tableau::whereDoesntHave('categorie', function ($q) use ($id) {
            $q->where('id', $id);
        })->get();

        return view('categories.show', compact('categorie', 'tableauxExistants'));
    }

    public function edit($id)
    {
        $categorie = Categorie::findOrFail($id);
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'required|string|max:250|unique:categories,libelle,' . $id
        ]);

        $categorie = Categorie::findOrFail($id);
        $categorie->update($request->only(['libelle']));

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès');
    }

    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);

        if ($categorie->tableaux()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des tableaux');
        }

        $categorie->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }
    public function attachTableau(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        foreach ($request->tableaux as $tableauId) {
            $tableau = Tableau::find($tableauId);
            if ($tableau) {
                $tableau->categorie_id = $categorie->id;
                $tableau->save();
            }
        }

        return redirect()->route('categories.show', $id)
            ->with('success', 'Tableaux ajoutés avec succès');
    }
}
