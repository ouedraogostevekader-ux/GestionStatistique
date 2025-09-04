@extends('layouts.admin')

{{-- @section('title', 'Programmes Budgétaires') --}}

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📋 Liste des Programmes Budgétaires</h3>
                    </div>
                    <div class="card-body">
                        @forelse($programmes as $programme)
                            <div class="programme-item mb-4">
                                <div class="card border-left-primary">
                                    <div class="card-header bg-primary text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">
                                                Programme {{ $programme->numero_programme }} : {{ $programme->libelle }}
                                            </h4>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-light btn-sm">
                                                    <i class="fas fa-eye"></i> Détails
                                                </a>
                                                <a href="#" class="btn btn-success btn-sm">
                                                    <i class="fas fa-table"></i> Tableau
                                                </a>
                                                <a href="#" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-chart-line"></i> Suivi
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Statistiques rapides -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-bullseye"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Objectifs Stratégiques</span>
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
                                                    <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Budget Total</span>
                                                        <span class="info-box-number">
                                                            {{ number_format(
                                                                $programme->objectifsStrategiques->sum(function ($obj) {
                                                                    return $obj->actions->sum(function ($action) {
                                                                        return $action->objectifsSpecifiques->sum(function ($objSpec) {
                                                                            return $objSpec->activites->sum('cout_previsionnel');
                                                                        });
                                                                    });
                                                                }),
                                                            ) }}
                                                            FCFA
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Aperçu hiérarchique -->
                                        <div class="hierarchy-preview">
                                            @foreach ($programme->objectifsStrategiques->take(2) as $objectif)
                                                <div class="objectif-preview mb-2">
                                                    <strong>🎯 {{ $objectif->libelle }}</strong>
                                                    @foreach ($objectif->actions->take(2) as $action)
                                                        <div class="ml-3">
                                                            <small class="text-muted">▸ {{ $action->libelle }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                            @if ($programme->objectifsStrategiques->count() > 2)
                                                <small class="text-muted">... et
                                                    {{ $programme->objectifsStrategiques->count() - 2 }} autres
                                                    objectifs</small>
                                            @endif
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
