@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>Détails de l’Activité</h3>
        <ul class="list-group">
            <li class="list-group-item"><strong>Libellé :</strong> {{ $activite->libelle }}</li>
            <li class="list-group-item"><strong>Période :</strong> {{ $activite->periode }}</li>
            <li class="list-group-item"><strong>Coût prévisionnel :</strong>
                {{ number_format($activite->cout_previsionnel, 2, ',', ' ') }} FCFA</li>
            <li class="list-group-item"><strong>Objectif spécifique :</strong>
                {{ $activite->objectifSpecifique->libelle ?? '-' }}</li>
        </ul>
        <div class="mt-3">
            <a href="{{ route('activites.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
@endsection
