@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Les objectifs spécifiques</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right" href="{{ route('objectifs-specifiques.create') }}">
                        Ajouter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="col-sm-10">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example1">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Action</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($objectifs as $objectif)
                                <tr>
                                    <td>{{ $objectif->libelle }}</td>
                                    <td> </td>
                                    <td>

                                        <div class='btn-group'>
                                            {{-- @can('objectifs-specifiques-list') --}}
                                            <a href="{{ route('objectifs-specifiques.show', $objectif->id) }}"
                                                class="btn btn-info btn-xs" title="Voir">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('objectifs-specifiques-edit') --}}
                                            <a href="{{ route('objectifs-specifiques.edit', $objectif->id) }}"
                                                class="btn btn-primary btn-xs" title="Modifier">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('objectifs-specifiques-delete') --}}
                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                title="Supprimer"
                                                data-url="{{ route('objectifs-specifiques.destroy', $objectif->id) }}"
                                                data-message="L'objectif spécifique <strong>{{ $objectif->libelle }}</strong> sera supprimé. Continuer ?">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            {{-- @endcan --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-right">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
