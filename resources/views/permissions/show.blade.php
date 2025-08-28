@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid px-4">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="mt-4">Permissions</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-5 d-flex justify-content-end">
                        <a class="btn btn-info" href="{{ route('permissions.index') }}">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('permissions.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
