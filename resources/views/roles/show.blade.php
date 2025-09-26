@extends('layouts.admin')
@section('breadcrumb', 'Roles')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Détails du rôle</li>
                    </ol>
                </div>
                <div class="col-sm-9">
                    @can('role-list')
                        <div class="mt-4 d-flex justify-content-end">
                            <a class="btn btn-secondary" href="{{ route('roles.index') }}"><i class="bi bi-arrow-left"></i> Retour
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('roles.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
