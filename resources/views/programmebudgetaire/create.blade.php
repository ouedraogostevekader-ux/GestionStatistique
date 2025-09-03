@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ajouter un Programme Budgétaire</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('programmebudgetaire.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé</label>
                            <input type="text" name="libelle" id="libelle" class="form-control" placeholder="Ex: Programme d’investissement" required>
                        </div>

                        <div class="mb-3">
                            <label for="montant" class="form-label">Numero programme</label>
                            <input type="number" name="montant" id="montant" class="form-control" placeholder="Ex: P0001" required>
                        </div>

                        <div class="mb-3">
                            <label for="Programme activité" class="form-label">Programme activité</label>
                            <input type="text" name="Programme activité" id="Programme activité" class="form-control" placeholder="Ex: ça sera un select" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
