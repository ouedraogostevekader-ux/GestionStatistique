{{-- resources/views/categories/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Détails de la catégorie')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Catégorie : {{ $categorie->libelle }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            Informations générales
        </div>
        <div class="card-body">
           
            <p><strong>Libellé :</strong> {{ $categorie->libelle }}</p>
            <p><strong>Nombre de tableaux :</strong> {{ $categorie->tableaux->count() }}</p>
        </div>
    </div>

    <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
        <span>Tableaux associés</span>
<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tableauxModal">
    + Ajouter un tableau
</button>
    </div>
      

        <div class="card-body">
            @if($categorie->tableaux->isEmpty())
                <p>Aucun tableau associé à cette catégorie.</p>
            @else
                <ul class="list-group">
                    @foreach($categorie->tableaux as $tableau)
                        <li class="list-group-item">
                            <strong>{{ $tableau->libelle ?? 'Sans titre' }}</strong><br>
                            
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Retour à la liste</a>
        <a href="{{ route('categories.edit', $categorie->id) }}" class="btn btn-primary">Modifier</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="tableauxModal" tabindex="-1" aria-labelledby="tableauxModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tableauxModalLabel">Sélectionner un tableau existant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('categories.attachTableau', $categorie->id) }}" method="POST">
          @csrf
          <div class="list-group">
            @foreach($tableauxExistants as $tableau)
              <label class="list-group-item">
                <input class="form-check-input me-1" type="checkbox" name="tableaux[]" value="{{ $tableau->libelle }}">
                {{ $tableau->libelle ?? 'Sans titre' }} 
              </label>
            @endforeach
          </div>
          <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">Associer les tableaux sélectionnés</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

