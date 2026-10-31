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
                            <a class="btn btn-success btn-export-loader"
                               href="{{ route('annuaire.export.excel', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger sous format Microsoft Excel">
                                <i class="fas fa-file-excel me-1"></i> <span>Exporter Excel (.xlsx)</span>
                            </a>
                            <a class="btn btn-danger btn-export-loader"
                               href="{{ route('annuaire.export.pdf', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger le document PDF au format paysage">
                                <i class="fas fa-file-pdf me-1"></i> <span>Exporter PDF</span>
                            </a>
                            <a class="btn btn-primary btn-export-loader"
                               href="{{ route('annuaire.export.word', ['version' => request('version', $versionActive->id), 'categorie' => request('categorie'), 'tableau' => request('tableau')]) }}"
                               title="Télécharger sous format Microsoft Word">
                                <i class="fas fa-file-word me-1"></i> <span>Exporter Word (.docx)</span>
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
            {{-- Bandeau de Consolidation & Verrouillage Officiel --}}
            @if ($versionActive)
                <div class="card shadow-sm border-0 mb-4 {{ $versionActive->isLocked() ? 'border-start border-4 border-success' : 'border-start border-4 border-primary' }}">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                                    <h5 class="fw-bold mb-0 me-2 text-dark">
                                        Consolidation : Édition {{ $versionActive->annee }} ({{ $versionActive->code }})
                                    </h5>
                                    @if ($versionActive->isLocked())
                                        <span class="badge bg-success px-3 py-1">
                                            <i class="fas fa-lock me-1"></i> Verrouillé / Publication Officielle
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-1">
                                            <i class="fas fa-hourglass-half me-1"></i> En cours de consolidation
                                        </span>
                                    @endif
                                </div>

                                @if ($consolidation)
                                    <div class="d-flex align-items-center gap-2 text-muted small mb-2 flex-wrap">
                                        <span><strong>{{ $consolidation['validees'] }} / {{ $consolidation['total'] }}</strong> validé(s)</span>
                                        <span>•</span>
                                        <span><strong>{{ $consolidation['transmises'] }}</strong> en revue DGESS</span>
                                        <span>•</span>
                                        <span><strong>{{ $consolidation['en_cours'] + $consolidation['en_attente'] + $consolidation['rejetees'] }}</strong> en attente de saisie</span>
                                    </div>

                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar {{ $consolidation['pourcentage'] == 100 ? 'bg-success' : 'bg-primary' }}" 
                                            role="progressbar" style="width: {{ $consolidation['pourcentage'] }}%;" 
                                            aria-valuenow="{{ $consolidation['pourcentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="text-end small text-muted mt-1">Taux d'exhaustivité validé : <strong>{{ $consolidation['pourcentage'] }}%</strong></div>
                                @endif
                            </div>

                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                @if (auth()->user()->isAdmin() || auth()->user()->structure?->sigle === 'DGESS')
                                    @if ($versionActive->isLocked())
                                        @if (auth()->user()->isAdmin())
                                            <form action="{{ route('versions.deverrouiller', $versionActive->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Attention : Déverrouiller l\'Annuaire permettra de nouveau la modification des données statistiques. Continuer ?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm">
                                                    <i class="fas fa-lock-open me-1"></i> Déverrouiller (Admin)
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('versions.verrouiller', $versionActive->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Confirmez-vous le verrouillage officiel de cette édition de l\'Annuaire ? Toutes les données statistiques seront scellées.')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm shadow-sm fw-bold">
                                                <i class="fas fa-shield-alt me-1"></i> Verrouiller pour Publication Officielle
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-export-loader').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Génération en cours...';
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.75';

                    // Rétablissement automatique du bouton après 8 secondes (temps habituel de téléchargement)
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.style.pointerEvents = 'auto';
                        this.style.opacity = '1';
                    }, 8000);
                });
            });
        });
    </script>
    @endpush
@endsection
