@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Modifier l'objectif spécifique</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-secondary float-right" href="{{ route('objectifs-specifiques.index') }}">
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations de l'objectif spécifique</h3>
            </div>

            <form action="{{ route('objectifs-specifiques.update', $objectifSpecifique->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="libelle">Libellé <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle"
                            name="libelle" value="{{ old('libelle', $objectifSpecifique->libelle) }}" required>
                        @error('libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="action_id">Action <span class="text-danger">*</span></label>
                        <select class="form-control @error('action_id') is-invalid @enderror" id="action_id"
                            name="action_id" required>
                            <option value="">Sélectionner une action</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action->id }}"
                                    {{ old('action_id', $objectifSpecifique->action_id) == $action->id ? 'selected' : '' }}>
                                    {{ $action->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('action_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('objectifs-specifiques.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
