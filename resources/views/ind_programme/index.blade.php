@extends('layouts.admin')
@section('breadcrumb', 'Indicateur')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des indicateurs<span
                                class="btn btn-success">({{ $indicateurs->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    @can('indicateur_Programme-create')
                        <a class="btn btn-primary float-right" href="{{ route('indicateurs-programmes.create') }}">
                            Ajouter
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </section>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h3>Filtrer les indicateurs</h3>
            </div>
            <form method="GET" action="{{ route('indicateurs-programmes.index') }}" class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="filter_type" class="form-label">Filtrer par</label>
                    <select name="filter_type" id="filter_type" class="form-select" onchange="updateFilterOptions()">
                        <option value="">-- Choisir un type --</option>
                        <option value="objectif_strategique"
                            {{ request('filter_type') == 'objectif_strategique' ? 'selected' : '' }}>
                            Objectif Stratégique
                        </option>
                        <option value="objectif_specifique"
                            {{ request('filter_type') == 'objectif_specifique' ? 'selected' : '' }}>
                            Objectif Spécifique
                        </option>
                        <option value="activite" {{ request('filter_type') == 'activite' ? 'selected' : '' }}>
                            Activité
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="filter_value" class="form-label"> </label>
                    <select name="filter_value" id="filter_value" class="form-select">
                        <option value="">-- Sélectionner une valeur --</option>

                        {{-- Si objectif stratégique est choisi --}}
                        @if (request('filter_type') == 'objectif_strategique')
                            @foreach ($objectifsStrategiques as $obj)
                                <option value="{{ $obj->id }}"
                                    {{ request('filter_value') == $obj->id ? 'selected' : '' }}>
                                    {{ $obj->libelle }}
                                </option>
                            @endforeach
                        @endif

                        {{-- Si objectif spécifique est choisi --}}
                        @if (request('filter_type') == 'objectif_specifique')
                            @foreach ($objectifsSpecifiques as $obj)
                                <option value="{{ $obj->id }}"
                                    {{ request('filter_value') == $obj->id ? 'selected' : '' }}>
                                    {{ $obj->libelle }}
                                </option>
                            @endforeach
                        @endif

                        {{-- Si activité est choisie --}}
                        @if (request('filter_type') == 'activite')
                            @foreach ($activites as $act)
                                <option value="{{ $act->id }}"
                                    {{ request('filter_value') == $act->id ? 'selected' : '' }}>
                                    {{ $act->libelle }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="{{ route('indicateurs-programmes.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="content container">
            <ul class="nav nav-tabs" id="indicateursTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="strat-tab" data-bs-toggle="tab" href="#strat" role="tab">
                        Objectifs Stratégiques
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="specif-tab" data-bs-toggle="tab" href="#specif" role="tab">
                        Objectifs Spécifiques
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="activite-tab" data-bs-toggle="tab" href="#activite" role="tab">
                        Activités
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-3" id="indicateursTabContent">

                <!-- Indicateurs Objectifs Stratégiques -->
                <div class="tab-pane fade show active" id="strat" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="stratEx">
                                <thead>
                                    <tr>
                                        <th>Objectif Stratégique</th>
                                        <th>Indicateurs</th>
                                        <th>Unité</th>
                                        <th>Valeur Cible</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($indicateursStrat as $ind)
                                        <tr>
                                            <td>{{ $ind->objectifStrategique->libelle ?? '-' }}</td>
                                            <td>{{ $ind->libelle }}</td>
                                            <td>{{ $ind->unite_mesure }}</td>
                                            <td>{{ $ind->valeur_cible }}</td>
                                            <td>
                                                <div class='btn-group'>
                                                    @can('indicateur_Programme-list')
                                                        <a href="{{ route('indicateurs-programmes.show', $ind->id) }}"
                                                            class="btn btn-info btn-xs" title="Voir">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('indicateur_Programme-edit')
                                                        <a href="{{ route('indicateurs-programmes.edit', $ind->id) }}"
                                                            class="btn btn-primary btn-xs" title="Modifier">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('indicateur_Programme-delete')
                                                        <button type="button" class="btn btn-outline-danger delete-btn"
                                                            title="Supprimer"
                                                            data-url="{{ route('indicateurs-programmes.destroy', $ind->id) }}"
                                                            data-message="L'indicateur <strong>{{ $ind->libelle }}</strong> sera supprimé. Continuer ?">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Indicateurs Objectifs Spécifiques -->
                <div class="tab-pane fade" id="specif" role="tabpanel">
                    <div class="card">
                        <div class="card-boday">
                            <table class="table table-bordered" id="specEx">
                                <thead>
                                    <tr>
                                        <th>Objectif Spécifique</th>
                                        <th>Indicateurs</th>
                                        <th>Unité</th>
                                        <th>Valeur cible</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Grouper les indicateurs par objectif spécifique
                                        $groupes = $indicateursSpecif->groupBy('objectif_specifique_id');
                                    @endphp

                                    @foreach ($groupes as $objectifSpecifiqueId => $indicateursGroupe)
                                        {{-- Objectif spécifique affiché une seule fois pour le groupe --}}
                                        @foreach ($indicateursGroupe as $index => $ind)
                                            <tr>
                                                @if ($index === 0)
                                                    <td rowspan="{{ $indicateursGroupe->count() }}">
                                                        {{ $ind->objectifSpecifique->libelle ?? '-' }}
                                                    </td>
                                                @endif

                                                <td>{{ $ind->libelle ?? '-' }}</td>
                                                <td>{{ $ind->unite_mesure ?? '-' }}</td>
                                                <td>{{ $ind->valeur_cible ?? '-' }}</td>
                                                <td>
                                                    <div class='btn-group'>
                                                        @can('indicateur_Programme-list')
                                                            <a href="{{ route('indicateurs-programmes.show', $ind->id) }}"
                                                                class="btn btn-info btn-xs" title="Voir">
                                                                <i class="bi bi-eye-fill"></i>
                                                            </a>
                                                        @endcan

                                                        @can('indicateur_Programme-edit')
                                                            <a href="{{ route('indicateurs-programmes.edit', $ind->id) }}"
                                                                class="btn btn-primary btn-xs" title="Modifier">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </a>
                                                        @endcan

                                                        @can('indicateur_Programme-delete')
                                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                                title="Supprimer"
                                                                data-url="{{ route('indicateurs-programmes.destroy', $ind->id) }}"
                                                                data-message="L'indicateur <strong>{{ $ind->libelle }}</strong> sera supprimé. Continuer ?">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Indicateurs Activités -->
                <div class="tab-pane fade" id="activite" role="tabpanel">
                    <div class="card">
                        <div class="card-boday">
                            <table class="table table-bordered" id="activiteEx">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th>Indicateurs</th>
                                        <th>Unité</th>
                                        <th>Valeur cible</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($indicateursActivite as $ind)
                                        <tr>
                                            <td>{{ $ind->activite?->libelle }}</td>
                                            <td>{{ $ind->libelle }}</td>
                                            <td>{{ $ind->unite_mesure }}</td>
                                            <td>{{ $ind->valeur_cible }}</td>
                                            <td>
                                                <div class='btn-group'>
                                                    @can('indicateur_Programme-list')
                                                        <a href="{{ route('indicateurs-programmes.show', $ind->id) }}"
                                                            class="btn btn-info btn-xs" title="Voir">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('indicateur_Programme-edit')
                                                        <a href="{{ route('indicateurs-programmes.edit', $ind->id) }}"
                                                            class="btn btn-primary btn-xs" title="Modifier">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('indicateur_Programme-delete')
                                                        <button type="button" class="btn btn-outline-danger delete-btn"
                                                            title="Supprimer"
                                                            data-url="{{ route('indicateurs-programmes.destroy', $ind->id) }}"
                                                            data-message="L'indicateur <strong>{{ $ind->libelle }}</strong> sera supprimé. Continuer ?">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="content px-3">
            <div class="clearfix"></div>

            <div class="card">
                <div class="card-body px-3">
                    {{-- <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="example2">
                            <thead>
                                <tr>
                                    <th>Objectif spécifique</th>
                                    <th>Objectif stratégique</th>
                                    <th>Activité</th>
                                    <th>Libellé</th>
                                    <th>Unité mesure</th>
                                    <th>Valeur</th>
                                    <th>Cible</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indicateurs as $indicateur)
                                    <tr>
                                        <td>
                                            @if ($indicateur->objectifSpecifique)
                                                {{ $indicateur->objectifSpecifique->libelle }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($indicateur->objectifStrategique)
                                                {{ $indicateur->objectifStrategique->libelle }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($indicateur->activite)
                                                {{ $indicateur->activite->libelle }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $indicateur->libelle }}</td>
                                        <td>{{ $indicateur->unite_mesure }}</td>
                                        <td>{{ $indicateur->valeur_ref }}</td>
                                        <td>{{ $indicateur->valeur_cible }}</td>

                                        <td>
                                            <div class='btn-group'>
                                                @can('indicateur_Programme-list')
                                                    <a href="{{ route('indicateurs-programmes.show', $indicateur->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                @endcan

                                                @can('indicateur_Programme-edit')
                                                    <a href="{{ route('indicateurs-programmes.edit', $indicateur->id) }}"
                                                        class="btn btn-primary btn-xs" title="Modifier">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endcan

                                                @can('indicateur_Programme-delete')
                                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                                        title="Supprimer"
                                                        data-url="{{ route('indicateurs-programmes.destroy', $indicateur->id) }}"
                                                        data-message="L'indicateur <strong>{{ $indicateur->libelle }}</strong> sera supprimé. Continuer ?">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        <div class="float-right">
                        </div>
                    </div>

                </div>
            </div>
        </div> --}}
        <script src="{{ asset('js/activites-hp-table.js') }}"></script>

    @endsection
