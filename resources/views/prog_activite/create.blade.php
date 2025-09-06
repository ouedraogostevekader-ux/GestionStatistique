@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Créer une nouvelle programme d'activité</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('programme-activites.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ old('libelle') }}" required>
            </div>

            {{-- <div class="form-group">
                <label for="annee">Année</label>
                <input type="number" class="form-control" id="annee" name="annee" value="{{ old('annee') }}"
                    required>
            </div> --}}
            <div class="form-group">
                <label for="file">Fichier (PDF, DOCX, XLSX)</label>
                <input type="file" class="form-control-file" id="file" name="file" accept=".pdf,.docx,.xlsx"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
@endsection
