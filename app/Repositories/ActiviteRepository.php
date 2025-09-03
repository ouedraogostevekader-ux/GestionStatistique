<?php

namespace App\Repositories;

use App\Models\Activite;

class ActiviteRepository
{
    public function all()
    {
        return Activite::paginate(1000);
    }

    public function find($id): ?Activite
    {
        return Activite::with('objectifSpecifique')->findOrFail($id);
    }

    public function create(array $data): Activite
    {
        return Activite::create($data);
    }

    public function update(Activite $activite, array $data): Activite
    {
        $activite->update($data);
        return $activite;
    }

    public function delete(Activite $activite): bool
    {
        return $activite->delete();
    }
}
