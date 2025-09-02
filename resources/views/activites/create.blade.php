@extends('layouts.admin')

@section('content')
    <div class="offset-2 container mt-4">
        <h3>Ajouter une Activité</h3>
        <form action="{{ route('activites.store') }}" method="POST">
            @include('activites.form')
        </form>
    </div>
@endsection
