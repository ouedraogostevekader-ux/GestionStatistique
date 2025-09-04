@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Détails du Programme Budgétaire</h5>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Libellé :</strong> {{ $programmeBudgetaire->libelle }}
                        </li>
                        <li class="list-group-item">
                            <strong>Code :</strong> {{ $programmeBudgetaire->numero_programme }}
                        </li>
                        <li class="list-group-item">
                            <strong>Structure</strong> {{($programmeBudgetaire->structures->isEmpty()) ? 'Aucune structure associée' : ''}}
                            @foreach($programmeBudgetaire->structures as $structure)
                                <span class="badge bg-secondary">{{ $structure->nom }}</span>
                            @endforeach
                        </li>
                       

<li class="list-group-item">
    <strong>Responsable actuel :</strong>
    @if($responsableActif)
        {{ $responsableActif->name }}
        <br>
        <small class="text-muted">
            Depuis le {{ \Carbon\Carbon::parse($responsableActif->pivot->date_debut)->format('d/m/Y') }}
        </small>
    @else
        <span class="text-muted">Aucun responsable assigné</span>
    @endif
</li>


                    </ul>

                    <div class="mt-4 text-end">
                        <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
