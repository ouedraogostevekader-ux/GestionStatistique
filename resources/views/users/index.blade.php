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
                    <a class="btn btn-primary float-right"
                       href="{{ route('users.create') }}">
                         Ajouter
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="col-sm-10">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="clearfix"></div>

        <div class="card">
            @include('users.table')
        </div>
    </div>

@endsection
