@extends('layouts.admin')
@section('breadcrumb', 'Activités')

@section('content')
    <div class="offset-2 container mt-4">
        <h3>Ajouter une Activité hors programme</h3>
        <form action="{{ route('rapportActivites.hors.programme.store') }}" method="POST">
            @include('activites.form-horsprogramme')
        </form>
    </div>
@endsection
