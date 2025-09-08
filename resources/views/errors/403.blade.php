@extends('layouts.admin')

@section('title', 'Accès refusé')

@section('content')
    <div class="flex items-center justify-center h-screen bg-gray-100">
        <div class="bg-white shadow-lg rounded-2xl p-8 text-center">
            <h1 class="text-6xl font-bold text-red-600">403</h1>
            <h2 class="text-2xl font-semibold mt-4">Accès refusé</h2>
            <p class="mt-2 text-gray-600">
                Désolé, vous n'avez pas les autorisations nécessaires pour accéder à cette page.
            </p>

            <a href="{{ url()->previous() }}"
                class="btn btn-success inline-block mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                ⬅ Retour
            </a>
        </div>
    </div>
@endsection
