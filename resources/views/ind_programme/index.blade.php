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
                    <a class="btn btn-primary float-right" href="{{ route('indicateurs-programmes.create') }}">
                        Ajouter
                    </a>
                </div>
            </div>
        </div>
    </section>
 <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h3>Filtrer les indicateurs</h3>
            </div>
        
<form method="GET" action="{{ route('indicateurs-programmes.index') }}" class="row gy-3 gx-3 align-items-end mb-3">

    <!-- Objectif Spécifique -->
    <div class="col-12 col-md-4">
        <label for="objectif_specifique_id" class="form-label fw-bold">Objectif Spécifique</label>
        <select name="objectif_specifique_id" id="objectif_specifique_id" class="form-select">
            <option value="">-- Tous --</option>
            @foreach($objectifsSpecifiques as $obj)
                <option value="{{ $obj->id }}" {{ request('objectif_specifique_id') == $obj->id ? 'selected' : '' }}>
                    {{ $obj->libelle }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Objectif Stratégique -->
    <div class="col-12 col-md-4">
        <label for="objectif_strategique_id" class="form-label fw-bold">Objectif Stratégique</label>
        <select name="objectif_strategique_id" id="objectif_strategique_id" class="form-select">
            <option value="">-- Tous --</option>
            @foreach($objectifsStrategiques as $obj)
                <option value="{{ $obj->id }}" {{ request('objectif_strategique_id') == $obj->id ? 'selected' : '' }}>
                    {{ $obj->libelle }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Activité -->
    <div class="col-12 col-md-4">
        <label for="activite_id" class="form-label fw-bold">Activité</label>
        <select name="activite_id" id="activite_id" class="form-select">
            <option value="">-- Toutes --</option>
            @foreach($activites as $act)
                <option value="{{ $act->id }}" {{ request('activite_id') == $act->id ? 'selected' : '' }}>
                    {{ $act->libelle }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Boutons -->
    <div class="col-12 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="{{ route('indicateurs-programmes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-clockwise"></i> Réinitialiser
        </a>
    </div>
            </form>

 </div>


    <div class="content px-3">
        <div class="col-sm-10">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body px-3">
                <div class="table-responsive">
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
                                            {{-- @can('indicateur-list') --}}
                                            <a href="{{ route('indicateurs-programmes.show', $indicateur->id) }}"
                                                class="btn btn-info btn-xs" title="Voir">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('indicateur-edit') --}}
                                            <a href="{{ route('indicateurs-programmes.edit', $indicateur->id) }}"
                                                class="btn btn-primary btn-xs" title="Modifier">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('indicateur-delete') --}}
                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                title="Supprimer"
                                                data-url="{{ route('indicateurs-programmes.destroy', $indicateur->id) }}"
                                                data-message="L'indicateur <strong>{{ $indicateur->libelle }}</strong> sera supprimé. Continuer ?">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            {{-- @endcan --}}
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
    </div>
@endsection
