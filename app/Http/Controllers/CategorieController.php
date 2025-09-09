<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;


use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
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
            'libelle' => 'required|string|max:250|unique:categories,libelle'
        ]);

        Categorie::create([
            'id' => Str::ulid(),
            'libelle' => $request->libelle
        ]);

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès');
    }

    public function show($id)
    {
        $categorie = Categorie::with('tableaux')->findOrFail($id);
        return view('categories.show', compact('categorie'));
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
}
