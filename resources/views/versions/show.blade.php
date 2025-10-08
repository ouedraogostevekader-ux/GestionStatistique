@extends('layouts.admin')
@section('breadcrumb', 'Versions')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Détails d'une version</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-4 d-flex justify-content-end">
                        <a class="btn btn-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Message erreur -->
@section('scripts')
    <script>
        // Masquer l'alerte après 5 secondes
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show'); // Cache l'alerte en douceur
                alert.classList.add('fade'); // Assure l'effet de fade-out
                setTimeout(() => alert.remove(), 5); // Retire complètement après la transition
            }
        }, 5);
    </script>
@endsection
<div class="content px-3">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('versions.show_fields')
            </div>
        </div>
    </div>
</div>
@endsection
