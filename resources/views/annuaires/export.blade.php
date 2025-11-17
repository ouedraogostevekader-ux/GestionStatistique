<h2>Annuaire Statistique - {{ $version->annee }}</h2>

@foreach ($tableaux as $tableau)
    <h4>{{ $tableau->libelle }}</h4>
    @include('annuaires.table')
@endforeach
