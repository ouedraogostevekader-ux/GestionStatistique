<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStructureRequest;
use App\Http\Requests\StructureRequest;
use App\Models\Structure;
use App\Repositories\StructureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StructureController extends Controller
{
    protected $structureRepository;

    public function __construct(StructureRepository $structureRepository)
    {
        $this->structureRepository = $structureRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $structures = $this->structureRepository->getAll();
        $types = $this->structureRepository->getUniqueTypes();

        return view('structures.index', compact('structures', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('structures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStructureRequest $request): RedirectResponse
    {
        try {
            $this->structureRepository->create($request->validated());

            return redirect()->route('structures.index')
                ->with('success', 'Structure créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la structure.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $structure = $this->structureRepository->findById($id);

        if (!$structure) {
            abort(404, 'Structure non trouvée');
        }

        return view('structures.show', compact('structure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $structure = $this->structureRepository->findById($id);

        if (!$structure) {
            abort(404, 'Structure non trouvée');
        }

        return view('structures.edit', compact('structure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreStructureRequest $request, string $id): RedirectResponse
    {
        try {
            $updated = $this->structureRepository->update($id, $request->validated());

            if (!$updated) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Structure non trouvée.');
            }

            return redirect()->route('structures.index')
                ->with('success', 'Structure modifiée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification de la structure.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $deleted = $this->structureRepository->delete($id);

            if (!$deleted) {
                return redirect()->back()
                    ->with('error', 'Structure non trouvée.');
            }

            return redirect()->route('structures.index')
                ->with('success', 'Structure supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la structure.');
        }
    }

    /**
     * Search structures
     */
    public function search(Request $request): View
    {
        $search = $request->get('search');
        $structures = $this->structureRepository->search($search);
        $types = $this->structureRepository->getUniqueTypes();

        return view('admin.structures.index', compact('structures', 'types', 'search'));
    }

    /**
     * Filter structures by type
     */
    public function filterByType(Request $request): View
    {
        $type = $request->get('type');
        $structures = $type ? $this->structureRepository->filterByType($type) : $this->structureRepository->getAll();
        $types = $this->structureRepository->getUniqueTypes();

        return view('admin.structures.index', compact('structures', 'types', 'type'));
    }
}
