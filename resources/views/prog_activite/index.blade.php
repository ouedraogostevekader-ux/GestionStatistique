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
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <h3 class="card-title mb-2 mb-md-0">📋 Liste des Programmes Budgétaires</h3>
                            <div class="card-tools">
                                <a href="{{ route('programme.liste') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus d-none d-sm-inline"></i>
                                    <span class="d-none d-sm-inline">Afficher le</span> programme d'activité
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($programmes as $programme)
                            <hr class="d-none d-md-block">
                            <div class="d-flex flex-column flex-md-row align-items-start mb-3"
                                onclick="event.stopPropagation();">
                                <a href="{{ route('programme-activites.show', $programme->id) }}"
                                    class="btn btn-danger btn-sm mb-2 mb-md-0">
                                    <i class="bi bi-file"></i>
                                    <span class="d-none d-sm-inline">Visualiser le</span> programme
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
                                                aria-controls="programmeContent{{ $programme->id }}" id="tabBudgetaire">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <span class="text-break">
                                                        <span class="d-none d-md-inline">Programme</span>
                                                        {{ $programme->numero_programme }} :
                                                        {{ $programme->libelle }}
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="programmeContent{{ $programme->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="programmeHeader{{ $programme->id }}"
                                            data-bs-parent="#programmeAccordion{{ $programme->id }}">
                                            <div class="accordion-body">

                                                <!-- Statistiques rapides -->
                                                <div class="row mb-4">
                                                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
                                                        <div class="info-box bg-info">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-bullseye"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">
                                                                    <span class="d-none d-md-inline">Objectifs</span>
                                                                    <span class="d-md-none">Obj.</span> Stratégiques
                                                                </span>
                                                                <span
                                                                    class="info-box-number">{{ $programme->objectifsStrategiques->count() }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
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
                                                    <div class="col-sm-6 col-lg-3 mb-3 mb-sm-0">
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
                                                    <div class="col-sm-6 col-lg-3">
                                                        <div class="info-box bg-danger">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-money-bill"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">
                                                                    <span class="d-none d-sm-inline">Coût Total</span>
                                                                    <span class="d-sm-none">Total</span>
                                                                </span>
                                                                <span class="info-box-number small">
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
                                                                    <span class="d-none d-sm-inline">FCFA</span>
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
                                                                <button class="accordion-button collapsed text-break"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#objectifContent{{ $programme->id }}_{{ $objIndex }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="objectifContent{{ $programme->id }}_{{ $objIndex }}">
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
                                                                                            <th class="w-40">Indicateur
                                                                                            </th>
                                                                                            <th
                                                                                                class="w-20 d-none d-sm-table-cell">
                                                                                                Unité</th>
                                                                                            <th class="w-20">Référence
                                                                                            </th>
                                                                                            <th class="w-20">Cible</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach ($objectif->indicateurs as $indicateur)
                                                                                            <tr>
                                                                                                <td class="text-break">
                                                                                                    {{ $indicateur->libelle }}
                                                                                                    <div
                                                                                                        class="d-sm-none small text-muted">
                                                                                                        {{ $indicateur->unite_mesure }}
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td
                                                                                                    class="d-none d-sm-table-cell">
                                                                                                    {{ $indicateur->unite_mesure }}
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
                                                                                        class="accordion-button collapsed text-break"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                        id="actionColapse">
                                                                                        {{ $action->libelle }}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="actionContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="actionHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}"
                                                                                    data-bs-parent="#actionAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}">
                                                                                    <div class="accordion-body">

                                                                                        <!-- Objectifs Spécifiques -->
                                                                                        @foreach ($action->objectifsSpecifiques as $objSpecIndex => $objSpec)
                                                                                            <div class="accordion mt-2"
                                                                                                id="objSpecAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                <div
                                                                                                    class="accordion-item">
                                                                                                    <h2 class="accordion-header"
                                                                                                        id="objSpecHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                        <button
                                                                                                            class="accordion-button collapsed text-break"
                                                                                                            type="button"
                                                                                                            data-bs-toggle="collapse"
                                                                                                            data-bs-target="#objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                            aria-expanded="false"
                                                                                                            aria-controls="objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                            id="osColapse">
                                                                                                            {{ $objSpec->libelle }}
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="objSpecContent{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                        class="accordion-collapse collapse"
                                                                                                        aria-labelledby="objSpecHeader{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}"
                                                                                                        data-bs-parent="#objSpecAccordion{{ $programme->id }}_{{ $objIndex }}_{{ $actionIndex }}_{{ $objSpecIndex }}">
                                                                                                        <div
                                                                                                            class="accordion-body">

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
                                                                                                                                    <th
                                                                                                                                        class="w-40">
                                                                                                                                        Indicateur
                                                                                                                                    </th>
                                                                                                                                    <th
                                                                                                                                        class="w-20 d-none d-sm-table-cell">
                                                                                                                                        Unité
                                                                                                                                    </th>
                                                                                                                                    <th
                                                                                                                                        class="w-20">
                                                                                                                                        Référence
                                                                                                                                    </th>
                                                                                                                                    <th
                                                                                                                                        class="w-20">
                                                                                                                                        Cible
                                                                                                                                    </th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>
                                                                                                                                @foreach ($objSpec->indicateurs as $indicateur)
                                                                                                                                    <tr>
                                                                                                                                        <td
                                                                                                                                            class="text-break">
                                                                                                                                            {{ $indicateur->libelle }}
                                                                                                                                            <div
                                                                                                                                                class="d-sm-none small text-muted">
                                                                                                                                                {{ $indicateur->unite_mesure }}
                                                                                                                                            </div>
                                                                                                                                        </td>
                                                                                                                                        <td
                                                                                                                                            class="d-none d-sm-table-cell">
                                                                                                                                            {{ $indicateur->unite_mesure }}
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
                                                                                                                        id="activiteColapse">
                                                                                                                        <div
                                                                                                                            class="card-body">
                                                                                                                            <h6 class="card-title text-break"
                                                                                                                                style="word-wrap: break-word; overflow-wrap: break-word; hyphens: auto;">
                                                                                                                                {{ $activite->libelle }}
                                                                                                                            </h6>

                                                                                                                            <div
                                                                                                                                class="row">
                                                                                                                                <div
                                                                                                                                    class="col-12 mb-2">
                                                                                                                                    <small><strong>Coût:</strong>
                                                                                                                                        {{ number_format($activite->cout_previsionnel, 0, ',', ' ') }}
                                                                                                                                        FCFA
                                                                                                                                    </small>
                                                                                                                                </div>
                                                                                                                                <div
                                                                                                                                    class="col-12">
                                                                                                                                    <small><strong>Périodes:
                                                                                                                                        </strong></small>
                                                                                                                                    @if ($activite->periodes->count() > 0)
                                                                                                                                        <div class="d-flex flex-wrap"
                                                                                                                                            style="gap: 0.25rem;">
                                                                                                                                            @foreach ($activite->periodes as $periode)
                                                                                                                                                <span
                                                                                                                                                    class="badge bg-success"
                                                                                                                                                    style="font-size: 0.75em;">{{ $periode->libelle }}</span>
                                                                                                                                            @endforeach
                                                                                                                                        </div>
                                                                                                                                    @else
                                                                                                                                        <span
                                                                                                                                            class="text-muted">-</span>
                                                                                                                                    @endif
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
                                                                                                                                                    <th
                                                                                                                                                        class="w-40">
                                                                                                                                                        Indicateur
                                                                                                                                                    </th>
                                                                                                                                                    <th
                                                                                                                                                        class="w-20 d-none d-sm-table-cell">
                                                                                                                                                        Unité
                                                                                                                                                    </th>
                                                                                                                                                    <th
                                                                                                                                                        class="w-20">
                                                                                                                                                        Ref
                                                                                                                                                    </th>
                                                                                                                                                    <th
                                                                                                                                                        class="w-20">
                                                                                                                                                        Cible
                                                                                                                                                    </th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                @foreach ($activite->indicateurs as $indicateur)
                                                                                                                                                    <tr>
                                                                                                                                                        <td
                                                                                                                                                            class="text-break">
                                                                                                                                                            <small>{{ $indicateur->libelle }}</small>
                                                                                                                                                            <div class="d-sm-none"
                                                                                                                                                                style="font-size: 0.7em;">
                                                                                                                                                                {{ $indicateur->unite_mesure }}
                                                                                                                                                            </div>
                                                                                                                                                        </td>
                                                                                                                                                        <td
                                                                                                                                                            class="d-none d-sm-table-cell">
                                                                                                                                                            <small>{{ $indicateur->unite_mesure }}</small>
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
                                <i class="fas fa-info-circle fa-2x fa-md-3x mb-3"></i>
                                <h4 class="h5 h-md-4">Aucun programme budgétaire</h4>
                                <p class="mb-0">Il n'y a pas encore de programmes budgétaires enregistrés.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
