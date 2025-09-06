{{-- resources/views/programme-activites/index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Programmes d'Activité</h3>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($programmesActivites->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Libellé</th>
                                            <th>Année</th>
                                            <th>Date de génération</th>
                                            <th>Fichiers</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($programmesActivites as $programme)
                                            <tr>
                                                <td>{{ $programme->libelle }}</td>
                                                <td>{{ $programme->annee }}</td>
                                                <td>{{ $programme->date_generation->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if ($programme->file_pdf)
                                                            <a href="{{ Storage::url($programme->file_pdf) }}"
                                                                class="btn btn-outline-danger" target="_blank"
                                                                title="Voir le PDF">
                                                                <i class="fas fa-file-pdf"></i> PDF
                                                            </a>
                                                        @endif

                                                        @if ($programme->file_excel && Storage::disk('public')->exists($programme->file_excel))
                                                            <a href="{{ Storage::url($programme->file_excel) }}"
                                                                class="btn btn-outline-success" download>
                                                                <i class="fas fa-file-excel"></i> Excel
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Excel indisponible</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun programme d'activité</h4>
                                <p class="text-muted">Commencez par créer votre premier programme d'activité.</p>
                                <a href="{{ route('programmes.tableau') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Créer un programme
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
