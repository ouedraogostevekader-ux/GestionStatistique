@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Utilisateurs</h1>
                </div>
                <div class="col-sm-6">
                    @can('user-create')
                        <a class="btn btn-primary float-right" href="{{ route('users.create') }}">
                            Ajouter
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="col-sm-10">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
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
        {{-- @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif --}}
    </div>
    <div class="clearfix"></div>

    <div class="card">
        @include('users.table')
    </div>
</div>
@endsection
