@extends('layouts.admin')
@section('breadcrumb', 'Versions')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des versions <span
                                class="btn btn-info">({{ $versions->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    @can('version-create')
                        <div class="mt-4 d-flex justify-content-end">
                            <a class="btn btn-primary float-right" href="{{ route('versions.create') }}">
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
        @include('versions.table')
    </div>
</div>
@endsection
