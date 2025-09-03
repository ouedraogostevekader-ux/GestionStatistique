@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Modifier un Programme Budgétaire</h2>

 <form action="{{ route('programmebudgetaire.update', $ProgrammeBudgetaire->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="libelle" class="form-label">Libellé</label>
        <input type="text" name="libelle" value="{{ $ProgrammeBudgetaire->libelle }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="numero_programme" class="form-label">Numéro programme</label>
        <input type="text" name="numero_programme" value="{{ $ProgrammeBudgetaire->numero_programme }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="structures" class="form-label">Structures concernées</label>
        <select name="structures[]" id="structures" class="form-select" multiple required>
            @foreach($structures as $structure)
                <option value="{{ $structure->id }}"
                    @if($ProgrammeBudgetaire->structures->contains($structure->id)) selected @endif>
                    {{ $structure->nom }}
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
