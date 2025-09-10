@extends('layouts.admin')
@section('breadcrumb', 'Utilisateurs')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Utilisateurs : <span class="btn btn-info">({{ $users->count() }})</span></h3>
                </div>
                <div class="col-sm-6">
                    @can('user-create')
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-primary float-right" href="{{ route('users.create') }}">
                                Ajouter
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="col-sm-10">

            <!-- Message erreur -->
        @section('scripts')
            <script>
                // Masquer l'alerte après 5 secondes
                setTimeout(() => {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.classList.remove('show'); // Cache l'alerte en douceur
                        alert.classList.add('fade'); // Assure l'effet de fade-out
                        setTimeout(() => alert.remove(), 500); // Retire complètement après la transition
                    }
                }, 5000);
            </script>
        @endsection

    </div>
    <div class="clearfix"></div>

    <div class="card">
        @include('users.table')
    </div>
</div>
@endsection
