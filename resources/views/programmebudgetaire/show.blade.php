@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Détails du Programme Budgétaire</h5>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Libellé :</strong> {{ $ProgrammeBudgetaire->libelle }}
                        </li>
                        <li class="list-group-item">
                            <strong>Code :</strong> {{ $ProgrammeBudgetaire->numero_programme }}
                        </li>
                        <li class="list-group-item">
                            <strong>Structure</strong> {{($ProgrammeBudgetaire->structure_id)
                                                                                    }}
                        </li>
                    </ul>

                    <div class="mt-4 text-end">
                        <a href="{{ route('programmebudgetaire.index') }}" class="btn btn-outline-secondary">
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
