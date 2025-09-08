@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ajouter un Programme Budgétaire</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('programmebudgetaire.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="libelle" class="form-label fw-bold">Libellé</label>
                            <input type="text" name="libelle" id="libelle" class="form-control" placeholder="Ex: Programme d’investissement" required>
                        </div>

                        <div class="mb-3">
                            <label for="numero_programme" class="form-label fw-bold">Numero programme</label>
                            <input type="number" name="numero_programme" id="numero_programme" class="form-control" placeholder="Ex: P0001" required>
                        </div>

<div class="mb-3">
    <label class="form-label fw-bold">Structures concernées</label>

    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
        @foreach($structures as $structure)
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="structures[]" value="{{ $structure->id }}"
                       id="structure_{{ $structure->id }}">
                <label class="form-check-label" for="structure_{{ $structure->id }}">
                    {{ $structure->nom }}
                </label>
            </div>
        @endforeach
    </div>

    <small class="form-text text-muted">Cochez les structures concernées. Faites défiler pour voir toutes les options.</small>
</div>


              <div class="mb-3">
                <label for="Responsable du programme" class="form-label fw-bold">Responsable du programme</label>
                <select name="responsable_id" id="responsable_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
        </div>


                        <div class="d-flex justify-content-between">
                            <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
