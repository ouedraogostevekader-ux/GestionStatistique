@extends('layouts.admin')
@section('breadcrumb', 'Activités')

@section('content')
    <div class="container mt-4">
        <h3>Détails de l'Activité</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informations de base</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Libellé :</strong> {{ $activite->libelle }}
                            </li>
                            <li class="list-group-item">
                                <strong>Coût prévisionnel :</strong>
                                {{ number_format($activite->cout_previsionnel, 2, ',', ' ') }} FCFA
                            </li>
                            <li class="list-group-item">
                                <strong>Objectif spécifique :</strong>
                                {{ $activite->objectifSpecifique->libelle ?? '-' }}
                            </li>
                            <li class="list-group-item">
                                <strong>Action :</strong>
                                {{ $activite->objectifSpecifique->action->libelle ?? '-' }}
                            </li>
                            <li class="list-group-item">
                                <strong>Objectif stratégique :</strong>
                                {{ $activite->objectifSpecifique->action->objectifStrategique->libelle ?? '-' }}
                            </li>
                            <li class="list-group-item">
                                <strong>Programme budgétaire :</strong>
                                {{ $activite->objectifSpecifique->action->objectifStrategique->programmeBudgetaire->libelle ?? '-' }}
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <h5>Périodes et indicateurs</h5>

                        <!-- Périodes associées -->
                        <div class="mb-3">
                            <strong>Périodes :</strong>
                            @if ($activite->periodes->count() > 0)
                                <div class="mt-2">
                                    @foreach ($activite->periodes as $periode)
                                        <span class="badge bg-primary me-1 mb-1">
                                            {{ $periode->libelle }} {{ $periode->pivot->annee }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Aucune période associée</span>
                            @endif
                        </div>

                        <!-- Indicateurs associés -->
                        <div class="mb-3">
                            <strong>Indicateurs :</strong>
                            @if ($activite->objectifSpecifique->indicateursProgrammes->count() > 0)
                                <ul class="list-group mt-2">
                                    @foreach ($activite->objectifSpecifique->indicateursProgrammes as $indicateur)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $indicateur->libelle }}</span>
                                                <span class="badge bg-info">
                                                    {{ $indicateur->valeur_cible }} {{ $indicateur->unite_mesure }}
                                                </span>
                                            </div>
                                            @if ($indicateur->description)
                                                <small class="text-muted">{{ $indicateur->description }}</small>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Aucun indicateur associé</span>
                            @endif
                        </div>

                        <!-- Suivis récents -->
                        <div class="mb-3">
                            <strong>Derniers suivis :</strong>
                            @php
                                $suivis = $activite->objectifSpecifique->indicateursProgrammes->flatMap->suivis
                                    ->sortByDesc('created_at')
                                    ->take(3);
                            @endphp

                            @if ($suivis->count() > 0)
                                <div class="mt-2">
                                    @foreach ($suivis as $suivi)
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between">
                                                    <small>{{ $suivi->indicateurProgramme->libelle }}</small>
                                                    <span
                                                        class="badge bg-{{ $suivi->resultat_atteint >= 100 ? 'success' : ($suivi->resultat_atteint >= 50 ? 'warning' : 'danger') }}">
                                                        {{ $suivi->resultat_atteint }}%
                                                    </span>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $suivi->created_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Aucun suivi enregistré</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('activites.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('activites.edit', $activite->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <form action="{{ route('activites.destroy', $activite->id) }}" method="POST"
                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
@endsection
