@extends('layouts.admin')
@section('breadcrumb', 'Objectifs stratégiques')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h3>
                        Modifier objectif strategique
                    </h3>
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
            <form method="POST" action="{{ route('objectifStrategiques.update', $objectifStrategique->id) }}">
                @csrf
                @method('PATCH')

                <div class="card-body">
                    <div class="row">
                        @include('objectifStrategiques.fields')
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('objectifStrategiques.index') }}" class="btn btn-default"> Annuler </a>
                </div>

            </form>

        </div>
    </div>
@endsection
