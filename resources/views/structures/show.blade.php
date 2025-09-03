@extends('layouts.admin')
@section('breadcrumb', 'Détails de la structure')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('structures.index') }}">Structures</a></li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <a class="btn btn-primary" href="{{ route('structures.edit', $structure->id) }}">
                            <i class="bi bi-pencil-fill"></i> Modifier
                        </a>
                        <a class="btn btn-secondary" href="{{ route('structures.index') }}">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-building"></i> Détails de la structure
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <h4 class="text-primary">{{ $structure->nom }}</h4>
                                <p class="text-muted mb-0">{{ $structure->sigle }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nom de la structure :</strong>
                                <p class="form-control-plaintext">{{ $structure->nom }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Sigle :</strong>
                                <p class="form-control-plaintext">{{ $structure->sigle }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Type de structure :</strong>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $structure->type_structure }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Date de création :</strong>
                                <p class="form-control-plaintext">{{ $structure->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        @if ($structure->updated_at != $structure->created_at)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Dernière modification :</strong>
                                    <p class="form-control-plaintext">{{ $structure->updated_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <form action="{{ route('structures.destroy', $structure->id) }}" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette structure ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash-fill"></i> Supprimer
                                </button>
                            </form>

                            <div>
                                <a href="{{ route('structures.edit', $structure->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-fill"></i> Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
