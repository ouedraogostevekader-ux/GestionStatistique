@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Détails de l'indicateur</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a class="btn btn-secondary" href="{{ route('indicateurs-programmes.index') }}">Retour</a>
                        <a class="btn btn-primary"
                            href="{{ route('indicateurs-programmes.edit', $indicateur->id) }}">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations générales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Libellé :</dt>
                            <dd class="col-sm-8">{{ $indicateur->libelle }}</dd>

                            <dt class="col-sm-4">Unité de mesure :</dt>
                            <dd class="col-sm-8">{{ $indicateur->unite_mesure }}</dd>

                            <dt class="col-sm-4">Valeur de référence :</dt>
                            <dd class="col-sm-8">{{ $indicateur->valeur_ref }}</dd>

                            <dt class="col-sm-4">Valeur cible :</dt>
                            <dd class="col-sm-8">{{ $indicateur->valeur_cible }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Objectif spécifique :</dt>
                            <dd class="col-sm-7">
                                @if ($indicateur->objectifSpecifique)
                                    {{ $indicateur->objectifSpecifique->libelle }}
                                @else
                                    <span class="text-muted">Aucun</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5">Objectif stratégique :</dt>
                            <dd class="col-sm-7">
                                @if ($indicateur->objectifStrategique)
                                    {{ $indicateur->objectifStrategique->libelle }}
                                @else
                                    <span class="text-muted">Aucun</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5">Activité :</dt>
                            <dd class="col-sm-7">
                                @if ($indicateur->activite)
                                    {{ $indicateur->activite->libelle }}
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5">Créé le :</dt>
                            <dd class="col-sm-7">{{ $indicateur->created_at->format('d/m/Y à H:i') }}</dd>

                            <dt class="col-sm-5">Modifié le :</dt>
                            <dd class="col-sm-7">{{ $indicateur->updated_at->format('d/m/Y à H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        @if ($indicateur->suivis->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Suivis associés</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Période</th>
                                    <th>Coût effectif</th>
                                    <th>Résultat atteint</th>
                                    <th>Observations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indicateur->suivis as $suivi)
                                    <tr>
                                        <td>{{ $suivi->periode }}</td>
                                        <td>{{ $suivi->cout_effectif ?? '-' }}</td>
                                        <td>{{ $suivi->resultat_atteint ?? '-' }}</td>
                                        <td>{{ $suivi->observations ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('suivis.show', $suivi->id) }}"
                                                class="btn btn-info btn-sm">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
