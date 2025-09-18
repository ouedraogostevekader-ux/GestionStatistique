@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>affecter un tableau</h4>
                </div>
                <div class="card-body">
<form method="POST" action="{{ route('tableaux.storeAffectation', $tableau->id) }}">
    @csrf

    <h4>Affecter le tableau : {{ $tableau->libelle }}</h4>

    <label>Structures :</label>
    <select name="structure_ids[]" multiple required>
        @foreach($structures as $structure)
            <option value="{{ $structure->id }}">{{ $structure->nom }}</option>
        @endforeach
    </select>

    <label>Version :</label>
    <input type="text" name="version">

    <label>Période :</label>
    <input type="text" name="periode">

    <label>Date début :</label>
    <input type="date" name="date_debut">

    <label>Date fin :</label>
    <input type="date" name="date_fin">

    <button type="submit">Affecter</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection