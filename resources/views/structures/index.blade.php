@extends('layouts.admin')
@section('breadcrumb', 'Structures')
@section('content')
    <section class="content-header">
        <div class="container px-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item active">Liste des structures<span
                                class="btn btn-info">({{ $structures->count() }})</span>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="mt-3 d-flex justify-content-end">
                        @can('structure-create')
                            <a class="btn btn-primary" id="btnAjouter" href="{{ route('structures.create') }}">
                                <i class="bi bi-plus"></i> Ajouter
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="mb-2 d-flex gap-3">
                <div>
                    <label for="filterType" class="form-label">Filtrer par type :</label>
                    <select id="filterType" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </section>

    <section class="content px-3">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nom</th>
                                <th>Sigle</th>
                                <th>Type de structure</th>
                                <th>Date création</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($structures as $key => $structure)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $structure->nom }}</td>
                                    <td>{{ $structure->sigle }}</td>
                                    <td>{{ $structure->type_structure }}</td>
                                    <td>{{ $structure->created_at->format('d/m/Y, H:i') }}</td>
                                    <td>
                                        <form action="{{ route('structures.destroy', $structure->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <div class='btn-group'>
                                                @can('structure-list')
                                                    <a href="{{ route('structures.show', $structure->id) }}"
                                                        class="btn btn-info btn-xs" title="Voir"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                @endcan

                                                @can('structure-edit')
                                                    <a href="{{ route('structures.edit', $structure->id) }}"
                                                        class="btn btn-primary btn-xs" title="Modifier"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                @endcan

                                                @can('structure-delete')
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Supprimer cette structure ?')"
                                                        title="Supprimer">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Nom</th>
                                <th>Sigle</th>
                                <th>Type de structure</th>
                                <th>Date création</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- Ton JS personnalisé -->
    <script src="{{ asset('js/structures-table.js') }}"></script>
@endsection
