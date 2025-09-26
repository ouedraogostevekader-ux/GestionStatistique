@extends('layouts.admin')
@section('breadcrumb', 'Utilisateurs')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Ajout d'un utilisateur</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        @include('users.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('users.index') }}" class="btn btn-default"> Annuler </a>
                </div>

            </form>

        </div>
    </div>
@endsection
