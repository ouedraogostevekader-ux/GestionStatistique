@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Modifier un Programme Budgétaire</h2>

 <form action="{{ route('programmebudgetaire.update', $programmeBudgetaire->id) }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="libelle" class="form-label">Libellé</label>
        <input type="text" name="libelle" value="{{ $programmeBudgetaire->libelle }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="numero_programme" class="form-label">Numéro programme</label>
        <input type="text" name="numero_programme" value="{{ $programmeBudgetaire->numero_programme }}" class="form-control" required>
    </div>
<div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
    @foreach($structures as $structure)
        <div class="form-check">
            <input class="form-check-input" type="checkbox"
                   name="structures[]" value="{{ $structure->id }}"
                   id="structure_{{ $structure->id }}"
                   {{ $programmeBudgetaire->structures->contains($structure->id) ? 'checked' : '' }}>
            <label class="form-check-label" for="structure_{{ $structure->id }}">
                {{ $structure->nom }}
            </label>
        </div>
    @endforeach
</div>

     <div class="mb-3">
    <label for="responsable_id" class="form-label fw-bold">Responsable du programme</label>
    <select name="responsable_id" id="responsable_id" class="form-select" required>
        @php
            $responsableActif = $programmeBudgetaire->responsables->firstWhere('pivot.date_fin', null);
        @endphp

        @foreach($users as $user)
            <option value="{{ $user->id }}"
                @if($responsableActif && $responsableActif->id === $user->id) selected @endif>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
</div>


    <div class="d-flex justify-content-between">
        <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </div>
</form>

</div>
@endsection
