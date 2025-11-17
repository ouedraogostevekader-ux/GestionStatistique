@extends('layouts.admin')
@section('breadcrumb', 'Utilisateurs')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Générer annuaire Statistique<span
                                class="btn btn-info">({{ $tableaux->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    {{-- @can('annuaire-create') --}}
                    <div class="mt-4 d-flex justify-content-end">
                        {{-- @can('exportWord') --}}
                        <a class="btn btn-primary float-right"
                            href="{{ route('annuaire.export.word', ['version' => request('version')]) }}">
                            Exporter en word
                        </a>
                        {{-- @endcan --}}
                        {{-- @can('exportPdf') --}}
                        <a class="btn btn-info float-right"
                            href="{{ route('annuaire.export.pdf', ['version' => request('version')]) }}">
                            Exporter en pdf
                        </a>
                        {{-- @endcan --}}
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

    <div class="container">
        <div>
            <form method="GET" action="{{ route('annuaires.index') }}" class="mt-3">
                <select name="version" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
                    <option value="">Sélectionner une version</option>
                    @foreach ($versions as $version)
                        <option value="{{ $version->id }}">
                            {{ $version->code }} - {{ $version->annee }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @if ($version)
            @foreach ($tableaux as $tableau)
                <h2>{{ $tableau->libelle }}</h2>
                <div class="card">
                    @include('annuaires.table')
                </div>
            @endforeach
        @endif
    </div>
    </div>
@endsection
