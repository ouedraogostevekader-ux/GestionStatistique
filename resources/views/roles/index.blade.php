@extends('layouts.admin')
@section('breadcrumb', 'Roles')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Listes des rôles <span
                                class="btn btn-info">({{ $roles->count() }})</span></li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    @can('role-create')
                        <div class="mt-4 d-flex justify-content-end">
                            <a class="btn btn-primary" href="{{ route('roles.create') }}">
                                Ajouter
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </section>
    <section class="content px-3">

        <div class="clearfix"></div>
        <div class="card">
            @include('roles.table')
        </div>
    </section>
@endsection
