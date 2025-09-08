@extends('layouts.admin')
@section('breadcrumb', 'Programme d\' activités')
@section('styles')
    <link rel="stylesheet" href="{{ asset('dist/css/pg_budgetaire.css') }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📋 Liste des Programmes Budgétaires</h3>
                        <div class="card-tools">
                            <a href="{{ route('programme.liste') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Afficher le programme d'activité
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($programmes as $programme)
                            <div class="btn-group me-3" onclick="event.stopPropagation();">
                                <a href="{{ route('programme-activites.show', $programme->id) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-table"></i> Afficher le tableau
                                </a>
                            </div>
                            <div class="programme-item mb-4">
                                <!-- Accordéon principal pour chaque programme -->
                                <div class="accordion" id="programmeAccordion{{ $programme->id }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="programmeHeader{{ $programme->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#programmeContent{{ $programme->id }}" aria-expanded="false"
                                                aria-controls="programmeContent{{ $programme->id }}"
                                                style="background-color: #007bff; color: white; font-weight: bold;">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <span>Programme {{ $programme->numero_programme }} :
                                                        {{ $programme->libelle }}</span>

                                                </div>
                                            </button>
                                        </h2>
                                        <div id="programmeContent{{ $programme->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="programmeHeader{{ $programme->id }}"
                                            data-bs-parent="#programmeAccordion{{ $programme->id }}">
                                            <div class="accordion-body">

                                                <!-- Statistiques rapides -->
                                                <div class="row mb-4">
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-info">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-bullseye"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Objectifs
                                                                    Stratégiques</span>
                                                                <span
                                                                    class="info-box-number">{{ $programme->objectifsStrategiques->count() }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-success">
                                                            <span class="info-box-icon"><i class="fas fa-tasks"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Actions</span>
                                                                <span class="info-box-number">
                                                                    {{ $programme->objectifsStrategiques->sum(function ($obj) {
                                                                        return $obj->actions->count();
                                                                    }) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-warning">
                                                            <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Activités</span>
                                                                <span class="info-box-number">
                                                                    {{ $programme->objectifsStrategiques->sum(function ($obj) {
                                                                        return $obj->actions->sum(function ($action) {
                                                                            return $action->objectifsSpecifiques->sum(function ($objSpec) {
                                                                                return $objSpec->activites->count();
                                                                            });
                                                                        });
                                                                    }) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="info-box bg-danger">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-money-bill"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Coût Total</span>
                                                                <span class="info-box-number">
                                                                    {{ number_format(
                                                                        $programme->objectifsStrategiques->sum(function ($obj) {
                                                                            return $obj->actions->sum(function ($action) {
                                                                                return $action->objectifsSpecifiques->sum(function ($objSpec) {
                                                                                    return $objSpec->activites->sum('cout_previsionnel');
                                                                                });
                                                                            });
                                                                        }),
                                                                        0,
                                                                        ',',
                                                                        ' ',
                                                                    ) }}
                                                                    FCFA
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Accordéons pour les Objectifs Stratégiques -->
                                                @foreach ($programme->objectifsStrategiques as $objIndex => $objectif)
                                                    <div class="accordion mt-3"
                                                        id="objectifAccordion{{ $programme->id }}_{{ $objIndex }}">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header"
                                                                id="objectifHeader{{ $programme->id }}_{{ $objIndex }}">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#objectifContent{{ $programme->id }}_{{ $objIndex }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="objectifContent{{ $programme->id }}_{{ $objIndex }}"
                                                                    style="background-color: #28a745; color: white; font-weight: bold;">
                                                                    {{ $objectif->libelle }}
                                                                </button>
                                                            </h2>
                                                            <div id="objectifContent{{ $programme->id }}_{{ $objIndex }}"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="objectifHeader{{ $programme->id }}_{{ $objIndex }}"
                                                                data-bs-parent="#objectifAccordion{{ $programme->id }}_{{ $objIndex }}">
                                                                <div class="accordion-body">

                                                                    <!-- Indicateurs de l'objectif stratégique -->
                                                                    @if ($objectif->indicateurs->count() > 0)
                                                                        <div class="mb-3">
                                                                            <h6><i class="fas fa-chart-line"></i>
                                                                                Indicateurs</h6>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-striped">
                                                                                    <thead class="table-primary">
                                                                                        <tr>
                                                                                            <th>Indicateur</th>
                                                                                            <th>Unité</th>
                                                                                            <th>Référence</th>
                                                                                            <th>Cible</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach ($objectif->indicateurs as $indicateur)
                                                                                            <tr>
                                                                                                <td>{{ $indicateur->libelle }}
                                                                                                </td>
                                                                                                <td>{{ $indicateur->unite_mesure }}
                                                                                                </td>
                                                                                                <td>{{ $indicateur->valeur_ref }}
                                                                                                </td>
                                                                                                <td>{{ $indicateur->valeur_cible }}
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Actions -->
                                                                    @foreach ($objectif->actions as $actionIndex => $action)
                                                                        <div class="accordion mt-2"
                                                                            id="actionAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}">
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="actionHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                        style="background-color: #17a2b8; color: white; margin-left: 20px;">
                                                                                        {{ $action->libelle }}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="actionHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                    data-bs-parent="#actionAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}">
                                                                                    <div class="accordion-body"
                                                                                        style="margin-left: 20px;">

                                                                                        <!-- Objectifs Spécifiques -->
                                                                                        @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                                                                            <div class="accordion mt-2"
                                                                                                id="objSpecAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                <div
                                                                                                    class="accordion-item">
                                                                                                    <h2 class="accordion-header"
                                                                                                        id="objSpecHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                        <button
                                                                                                            class="accordion-button collapsed"
                                                                                                            type="button"
                                                                                                            data-bs-toggle="collapse"
                                                                                                            data-bs-target="#objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                            aria-expanded="false"
                                                                                                            aria-controls="objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                            style="background-color: #ffc107; color: #212529; margin-left: 20px;">
                                                                                                            {{ $objSpec->libelle }}
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                        class="accordion-collapse collapse"
                                                                                                        aria-labelledby="objSpecHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                        data-bs-parent="#objSpecAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                        <div class="accordion-body"
                                                                                                            style="margin-left: 40px;">

                                                                                                            <!-- Indicateurs objectif spécifique -->
                                                                                                            @if ($objSpec->indicateurs->count() > 0)
                                                                                                                <div
                                                                                                                    class="mb-3">
                                                                                                                    <h6><i
                                                                                                                            class="fas fa-chart-bar"></i>
                                                                                                                        Indicateurs
                                                                                                                    </h6>
                                                                                                                    <div
                                                                                                                        class="table-responsive">
                                                                                                                        <table
                                                                                                                            class="table table-sm table-striped">
                                                                                                                            <thead
                                                                                                                                class="table-warning">
                                                                                                                                <tr>
                                                                                                                                    <th>Indicateur
                                                                                                                                    </th>
                                                                                                                                    <th>Unité
                                                                                                                                    </th>
                                                                                                                                    <th>Référence
                                                                                                                                    </th>
                                                                                                                                    <th>Cible
                                                                                                                                    </th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>
                                                                                                                                @foreach ($objSpec->indicateurs as $indicateur)
                                                                                                                                    <tr>
                                                                                                                                        <td>{{ $indicateur->libelle }}
                                                                                                                                        </td>
                                                                                                                                        <td>{{ $indicateur->unite_mesure }}
                                                                                                                                        </td>
                                                                                                                                        <td>{{ $indicateur->valeur_ref }}
                                                                                                                                        </td>
                                                                                                                                        <td>{{ $indicateur->valeur_cible }}
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                @endforeach
                                                                                                                            </tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            @endif

                                                                                                            <!-- Activités -->
                                                                                                            <div
                                                                                                                class="mb-3">
                                                                                                                <h6><i
                                                                                                                        class="fas fa-cogs"></i>
                                                                                                                    Activités
                                                                                                                </h6>
                                                                                                                @foreach ($objSpec->activites as $activiteIndex => $activite)
                                                                                                                    <div class="card mb-2"
                                                                                                                        style="border-left: 4px solid #fd7e14;">
                                                                                                                        <div
                                                                                                                            class="card-body p-3">
                                                                                                                            <h6 class="card-title"
                                                                                                                                style="color: #fd7e14;">

                                                                                                                                {{ $activite->libelle }}
                                                                                                                            </h6>

                                                                                                                            <div
                                                                                                                                class="row">
                                                                                                                                <div
                                                                                                                                    class="col-md-6">
                                                                                                                                    <small><strong>Coût:</strong>
                                                                                                                                        {{ number_format($activite->cout_previsionnel) }}
                                                                                                                                        FCFA</small>
                                                                                                                                </div>
                                                                                                                                <div
                                                                                                                                    class="col-md-6">
                                                                                                                                    @php
                                                                                                                                        $periodes = $activite->periodes
                                                                                                                                            ->pluck(
                                                                                                                                                'libelle',
                                                                                                                                            )
                                                                                                                                            ->toArray();
                                                                                                                                    @endphp
                                                                                                                                    <small><strong>Périodes:</strong>
                                                                                                                                        @if (in_array('T1', $periodes) || in_array('Trimestre 1', $periodes))
                                                                                                                                            <span
                                                                                                                                                class="badge bg-success">T1</span>
                                                                                                                                        @endif
                                                                                                                                        @if (in_array('T2', $periodes) || in_array('Trimestre 2', $periodes))
                                                                                                                                            <span
                                                                                                                                                class="badge bg-success">T2</span>
                                                                                                                                        @endif
                                                                                                                                        @if (in_array('T3', $periodes) || in_array('Trimestre 3', $periodes))
                                                                                                                                            <span
                                                                                                                                                class="badge bg-success">T3</span>
                                                                                                                                        @endif
                                                                                                                                        @if (in_array('T4', $periodes) || in_array('Trimestre 4', $periodes))
                                                                                                                                            <span
                                                                                                                                                class="badge bg-success">T4</span>
                                                                                                                                        @endif
                                                                                                                                    </small>
                                                                                                                                </div>
                                                                                                                            </div>

                                                                                                                            @if ($activite->indicateurs->count() > 0)
                                                                                                                                <div
                                                                                                                                    class="mt-2">
                                                                                                                                    <small><strong>Indicateurs:</strong></small>
                                                                                                                                    <div
                                                                                                                                        class="table-responsive">
                                                                                                                                        <table
                                                                                                                                            class="table table-sm table-bordered">
                                                                                                                                            <thead
                                                                                                                                                class="table-secondary">
                                                                                                                                                <tr>
                                                                                                                                                    <th>Indicateur
                                                                                                                                                    </th>
                                                                                                                                                    <th>Unité
                                                                                                                                                    </th>
                                                                                                                                                    <th>Ref
                                                                                                                                                    </th>
                                                                                                                                                    <th>Cible
                                                                                                                                                    </th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                @foreach ($activite->indicateurs as $indicateur)
                                                                                                                                                    <tr>
                                                                                                                                                        <td><small>{{ $indicateur->libelle }}</small>
                                                                                                                                                        </td>
                                                                                                                                                        <td><small>{{ $indicateur->unite_mesure }}</small>
                                                                                                                                                        </td>
                                                                                                                                                        <td><small>{{ $indicateur->valeur_ref }}</small>
                                                                                                                                                        </td>
                                                                                                                                                        <td><small>{{ $indicateur->valeur_cible }}</small>
                                                                                                                                                        </td>
                                                                                                                                                    </tr>
                                                                                                                                                @endforeach
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            @endif
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                @endforeach
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h4>Aucun programme budgétaire</h4>
                                <p>Il n'y a pas encore de programmes budgétaires enregistrés.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
