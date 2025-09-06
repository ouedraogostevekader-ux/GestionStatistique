@extends('layouts.admin')
@section('breadcrumb', 'Actions')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des activités<span
                                class="btn btn-success">({{ $actions->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        <a class="btn btn-primary" id="btnAjouter" href="{{ route('actions.create') }}">
                            <i class="bi bi-plus"></i> Ajouter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example2">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Objectif strategique</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($actions as $action)
                                <tr>
                                    <td>{{ $action->libelle }}</td>
                                    <td>{{ $action->objectifStrategique->libelle }}</td>
                                    <td>

                                        <div class='btn-group'>
                                            {{-- @can('action-list') --}}
                                            <a href="{{ route('actions.show', $action->id) }}" class="btn btn-info btn-xs"
                                                title="Voir">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('action-edit') --}}
                                            <a href="{{ route('actions.edit', $action->id) }}"
                                                class="btn btn-warning btn-xs" title="Modifier">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('actions-delete') --}}
                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                title="Supprimer" data-url="{{ route('actions.destroy', $action->id) }}"
                                                data-message="L'action <strong>{{ $action->libelle }}</strong> sera supprimée. Continuer ?">
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
