@extends('layouts.admin')
@section('breadcrumb', 'Utilisateurs')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>
                        Détails de l'utilisateur
                    </h3>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-info float-right" href="{{ route('users.index') }}">
                            Retour
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
                @include('users.show_fields')
            </div>
        </div>
    </div>
</div>
@endsection
