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
                        <a class="btn btn-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <a class="btn btn-primary"
                            href="{{ route('indicateurs-programmes.edit', $indicateur->id) }}">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Suivis associés</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addSuiviModal">
                        <i class="fas fa-plus"></i> Evaluer de nouveau
                    </button>
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

        <!-- Modal Ajouter suivi -->
        <div class="modal fade" id="addSuiviModal" tabindex="-1" role="dialog" aria-labelledby="addSuiviModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('suivis.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="indicateur_programme_id" value="{{ $indicateur->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSuiviModalLabel">Ajouter un suivi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Résultat atteint *</label>
                                <input type="number" step="0.01" class="form-control" name="resultat_atteint" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Coût effectif </label>
                                <input type="number" step="0.01" class="form-control" name="cout_effectif">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Période </label>
                                <input type="text" class="form-control" name="periode">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observation</label>
                                <textarea class="form-control" name="observation" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
