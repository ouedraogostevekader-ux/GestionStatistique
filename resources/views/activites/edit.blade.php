@extends('layouts.admin')
@section('breadcrumb', 'Activités')
@section('content')
    <div class="offset-2 container mt-4">
        <h3>Modifier une Activité</h3>
        <form action="{{ route('activites.update', $activite->id) }}" method="POST">
            @method('PUT')
            @include('activites.form')
        </form>
    </div>
@endsection
