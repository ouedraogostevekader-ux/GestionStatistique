@extends('layouts.admin')
@section('breadcrumb', 'Importation CSV')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/import.css') }}" />

    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Importation CSV</h1>
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Importation</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button class="btn btn-outline-info btn-sm" id="showInstructions">
                            <i class="bi bi-question-circle"></i> Instructions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <!-- Messages de feedback -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>Succès!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Erreur!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Instructions (masquées par défaut) -->
        <div class="row mb-4" id="instructionsCard" style="display: none;">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Instructions d'importation
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>📋 Structures</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Colonnes requises:</strong></li>
                                    <li>• <code>nom</code> - Nom complet</li>
                                    <li>• <code>sigle</code> - Sigle/Abréviation</li>
                                    <li>• <code>type_structure</code> - Type</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>🎯 Objectifs stratégiques</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Colonnes requises:</strong></li>
                                    <li>• <code>libelle</code> - Libellé</li>
                                    <li>• <code>programme_budgetaire</code> - Programme</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>🎯 Objectifs spécifiques</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Colonnes requises:</strong></li>
                                    <li>• <code>libelle</code> - Libellé</li>
                                    <li>• <code>action</code> - Action parent</li>
                                </ul>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Important:</strong> Assurez-vous que votre fichier CSV utilise des virgules comme
                            séparateurs et que les données sont encodées en UTF-8.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes d'importation -->
        <div class="row">
            <!-- Importation des structures -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 import-card" data-type="structures">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-building"></i> Structures
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Importez vos structures organisationnelles (directions, services, départements...).
                        </p>

                        <div class="mb-3">
                            <a href="{{ route('import.template', 'structures') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i> Télécharger le modèle
                            </a>
                        </div>

                        <form action="{{ route('import.structures') }}" method="POST" enctype="multipart/form-data"
                            class="import-form" id="structuresForm">
                            @csrf
                            <div class="mb-3">
                                <label for="structures_file" class="form-label">Fichier CSV</label>
                                <input type="file" class="form-control csv-file" id="structures_file" name="csv_file"
                                    accept=".csv,.txt" required>
                                <div class="form-text">Formats acceptés: CSV, TXT (max 10MB)</div>
                            </div>

                            <div class="file-preview mb-3" id="structures_preview" style="display: none;"></div>

                            <button type="submit" class="btn btn-primary w-100" disabled>
                                <i class="bi bi-upload"></i> Importer les structures
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                        <small><i class="bi bi-info-circle"></i> Colonnes: nom, sigle, type_structure</small>
                    </div>
                </div>
            </div>

            <!-- Importation des objectifs stratégiques -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 import-card" data-type="objectifs-strategiques">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-target"></i> Objectifs stratégiques
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Importez les objectifs stratégiques liés aux programmes budgétaires.
                        </p>

                        <div class="mb-3">
                            <a href="{{ route('import.template', 'objectifs-strategiques') }}"
                                class="btn btn-outline-success btn-sm">
                                <i class="bi bi-download"></i> Télécharger le modèle
                            </a>
                        </div>

                        <form action="{{ route('import.objectifs-strategiques') }}" method="POST"
                            enctype="multipart/form-data" class="import-form" id="objectifsStrategiquesForm">
                            @csrf
                            <div class="mb-3">
                                <label for="objectifs_strategiques_file" class="form-label">Fichier CSV</label>
                                <input type="file" class="form-control csv-file" id="objectifs_strategiques_file"
                                    name="csv_file" accept=".csv,.txt" required>
                                <div class="form-text">Formats acceptés: CSV, TXT (max 10MB)</div>
                            </div>

                            <div class="file-preview mb-3" id="objectifs_strategiques_preview" style="display: none;">
                            </div>

                            <button type="submit" class="btn btn-success w-100" disabled>
                                <i class="bi bi-upload"></i> Importer les objectifs stratégiques
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                        <small><i class="bi bi-info-circle"></i> Colonnes: libelle, programme_budgetaire</small>
                    </div>
                </div>
            </div>

            <!-- Importation des objectifs spécifiques -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 import-card" data-type="objectifs-specifiques">
                    <div class="card-header bg-warning text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-bullseye"></i> Objectifs spécifiques
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Importez les objectifs spécifiques liés aux actions.
                        </p>

                        <div class="mb-3">
                            <a href="{{ route('import.template', 'objectifs-specifiques') }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-download"></i> Télécharger le modèle
                            </a>
                        </div>

                        <form action="{{ route('import.objectifs-specifiques') }}" method="POST"
                            enctype="multipart/form-data" class="import-form" id="objectifsSpecifiquesForm">
                            @csrf
                            <div class="mb-3">
                                <label for="objectifs_specifiques_file" class="form-label">Fichier CSV</label>
                                <input type="file" class="form-control csv-file" id="objectifs_specifiques_file"
                                    name="csv_file" accept=".csv,.txt" required>
                                <div class="form-text">Formats acceptés: CSV, TXT (max 10MB)</div>
                            </div>

                            <div class="file-preview mb-3" id="objectifs_specifiques_preview" style="display: none;">
                            </div>

                            <button type="submit" class="btn btn-warning w-100" disabled>
                                <i class="bi bi-upload"></i> Importer les objectifs spécifiques
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                        <small><i class="bi bi-info-circle"></i> Colonnes: libelle, action</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques d'importation -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-bar-chart"></i> Statistiques d'importation récentes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="refreshStats">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4 class="text-primary" id="structuresCount">{{ App\Models\Structure::count() }}
                                    </h4>
                                    <p>Structures totales</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4 class="text-success" id="objectifsStrategiquesCount">
                                        {{ App\Models\ObjectifsStrategique::count() }}</h4>
                                    <p>Objectifs stratégiques</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h4 class="text-warning" id="objectifsSpecifiquesCount">
                                        {{ App\Models\ObjectifSpecifique::count() }}</h4>
                                    <p>Objectifs spécifiques</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des importations -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-clock-history"></i> Conseils et bonnes pratiques
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="bi bi-check-circle text-success"></i> Bonnes pratiques</h5>
                                <ul>
                                    <li>Téléchargez toujours le modèle CSV avant l'importation</li>
                                    <li>Vérifiez que vos données sont complètes</li>
                                    <li>Utilisez l'encodage UTF-8 pour les caractères spéciaux</li>
                                    <li>Évitez les doublons dans vos données</li>
                                    <li>Testez avec un petit fichier d'abord</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="bi bi-exclamation-triangle text-warning"></i> Points d'attention</h5>
                                <ul>
                                    <li>Les programmes budgétaires doivent exister avant d'importer les objectifs
                                        stratégiques</li>
                                    <li>Les actions doivent exister avant d'importer les objectifs spécifiques</li>
                                    <li>Les noms et sigles des structures doivent être uniques</li>
                                    <li>Taille maximum: 10MB par fichier</li>
                                    <li>En cas d'erreur, l'importation s'arrête et aucune donnée n'est sauvegardée</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de progression -->
    <div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i> Importation en cours
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p id="progressText">Traitement du fichier en cours...</p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 0%" id="progressBar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de prévisualisation -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye"></i> Aperçu du fichier CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="confirmImport">Procéder à l'importation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/import.js') }}"></script>
@endsection
