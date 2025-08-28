@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid px-4">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="mt-4">Roles</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Listes</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-5 d-flex justify-content-end">
                        <a class="btn btn-primary" href="{{ route('roles.create') }}">
                            Ajouter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content px-3">
        <div class="col-sm-10">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="clearfix"></div>
        <div class="card">
            @include('admin.roles.table')
        </div>
    </section>
@endsection
