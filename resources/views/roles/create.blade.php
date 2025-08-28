@extends('layouts/admin/admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        Ajouter Role
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <ul>
                            <li>{{ $error }}</li>
                        </ul>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('roles.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    <div class="row">
                        @include('admin.roles.fields')
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-default"> Annuler </a>
                </div>

            </form>

        </div>
    </div>
@endsection
