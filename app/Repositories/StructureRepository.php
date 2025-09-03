<?php

namespace App\Repositories;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StructureRepository
{
    protected $model;

    public function __construct(Structure $structure)
    {
        $this->model = $structure;
    }

    /**
     * Récupérer toutes les structures
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Récupérer les structures avec pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Trouver une structure par ID
     *
     * @param string $id
     * @return Structure|null
     */
    public function findById(string $id): ?Structure
    {
        return $this->model->find($id);
    }

    /**
     * Créer une nouvelle structure
     *
     * @param array $data
     * @return Structure
     */
    public function create(array $data): Structure
    {
        return $this->model->create($data);
    }

    /**
     * Mettre à jour une structure
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $structure = $this->findById($id);
        if (!$structure) {
            return false;
        }
        return $structure->update($data);
    }

    /**
     * Supprimer une structure
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $structure = $this->findById($id);
        if (!$structure) {
            return false;
        }
        return $structure->delete();
    }

    /**
     * Rechercher des structures par nom ou sigle
     *
     * @param string $search
     * @return Collection
     */
    public function search(string $search): Collection
    {
        return $this->model
            ->where('nom', 'like', "%{$search}%")
            ->orWhere('sigle', 'like', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Filtrer par type de structure
     *
     * @param string $type
     * @return Collection
     */
    public function filterByType(string $type): Collection
    {
        return $this->model
            ->where('type_structure', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Récupérer les types de structures uniques
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUniqueTypes(): \Illuminate\Support\Collection
    {
        return $this->model
            ->select('type_structure')
            ->distinct()
            ->orderBy('type_structure')
            ->pluck('type_structure');
    }

    /**
     * Vérifier si une structure existe par nom
     *
     * @param string $nom
     * @param string|null $excludeId
     * @return bool
     */
    public function existsByNom(string $nom, ?string $excludeId = null): bool
    {
        $query = $this->model->where('nom', $nom);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Vérifier si une structure existe par sigle
     *
     * @param string $sigle
     * @param string|null $excludeId
     * @return bool
     */
    public function existsBySigle(string $sigle, ?string $excludeId = null): bool
    {
        $query = $this->model->where('sigle', $sigle);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}