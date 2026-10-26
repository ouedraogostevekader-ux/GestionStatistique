@extends('layouts.admin')
@section('breadcrumb', 'Annuaire Statistique')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-3">
                <div class="col-md-5">
                    <h1 class="m-0 text-dark">
                        <i class="fas fa-book-open text-primary me-2"></i> Annuaire Statistique
                        <span class="badge badge-info ms-2" style="font-size: 0.8rem;">
                            {{ $tableaux->count() }} tableau(x)
                        </span>
                    </h1>
                </div>
                <div class="col-md-7 text-md-end mt-2 mt-md-0">
                    @if ($versionActive)
                        <div class="btn-group shadow-sm" role="group">
                            <a class="btn btn-success"
                               href="{{ route('annuaire.export.excel', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger sous format Microsoft Excel">
                                <i class="fas fa-file-excel me-1"></i> Exporter Excel (.xlsx)
                            </a>
                            <a class="btn btn-danger"
                               href="{{ route('annuaire.export.pdf', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger le document PDF au format paysage">
                                <i class="fas fa-file-pdf me-1"></i> Exporter PDF
                            </a>
                            <a class="btn btn-primary"
                               href="{{ route('annuaire.export.word', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger sous format Microsoft Word">
                                <i class="fas fa-file-word me-1"></i> Exporter Word (.docx)
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Barre de filtrage interactif --}}
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('annuaires.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label for="versionSelect" class="form-label fw-bold mb-1">
                                <i class="fas fa-calendar-alt text-muted me-1"></i> Version & Année :
                            </label>
                            <select id="versionSelect" name="version" onchange="this.form.submit()" class="form-control form-select">
                                <option value="">-- Sélectionner une version --</option>
                                @foreach ($versions as $v)
                                    <option value="{{ $v->id }}" {{ ($versionActive && $versionActive->id == $v->id) ? 'selected' : '' }}>
                                        Édition {{ $v->annee }} ({{ $v->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label for="categorieSelect" class="form-label fw-bold mb-1">
                                <i class="fas fa-folder text-muted me-1"></i> Catégorie Thématique :
                            </label>
                            <select id="categorieSelect" name="categorie" onchange="this.form.submit()" class="form-control form-select">
                                <option value="">-- Toutes les catégories --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end mt-4">
                            @if (request('categorie') || request('tableau'))
                                <a href="{{ route('annuaires.index', ['version' => request('version', $versionActive?->id)]) }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Réinitialiser filtres
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid pb-5">
        @if ($versionActive)
            @forelse ($tableaux as $tableau)
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            @if ($tableau->categorie)
                                <span class="badge bg-secondary text-uppercase mb-1" style="font-size: 0.75rem;">
                                    {{ $tableau->categorie->libelle }}
                                </span>
                            @endif
                            <h3 class="card-title text-primary fw-bold m-0 d-block" style="font-size: 1.15rem;">
                                {{ $tableau->libelle }}
                            </h3>
                        </div>
                    </div>
                    @include('annuaires.table')
                </div>
            @empty
                <div class="alert alert-info text-center p-5 shadow-sm">
                    <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
                    <h4>Aucun tableau disponible</h4>
                    <p class="text-muted m-0">Aucun tableau statistique ne correspond aux critères de sélection pour cette version.</p>
                </div>
            @endforelse
        @else
            <div class="alert alert-warning text-center p-5 shadow-sm">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                <h4>Sélectionnez une version</h4>
                <p class="text-muted m-0">Veuillez choisir une version dans la liste déroulante ci-dessus pour afficher l'Annuaire Statistique.</p>
            </div>
        @endif
    </div>
@endsection
