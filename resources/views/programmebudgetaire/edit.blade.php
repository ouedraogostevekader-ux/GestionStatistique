@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Modifier un Programme Budgétaire</h2>

    <form action="{{ route('programmebudgetaire.update', $ProgrammeBudgetaire->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Libellé</label>
            <input type="text" name="libelle" value="{{ $ProgrammeBudgetaire->libelle }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Numero programme</label>
            <input type="text" name="code" value="{{ $ProgrammeBudgetaire->code }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Programme activité</label>
            <input type="number" name="montant" value="{{ $ProgrammeBudgetaire->programme_activite_id }}" class="form-control" required>
        </div>



        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
