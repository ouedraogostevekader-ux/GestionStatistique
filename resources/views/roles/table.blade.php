<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="example2">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $key => $data)
                    <tr>
                        <td>{{ $key + 1 }} </td>
                        <td>{{ $data->name }}</td>
                        <td>
                            <form action="{{ route('roles.destroy', $data->ulid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    {{-- @can('role-list') --}}
                                    <a href="{{ route('roles.show', $data->ulid) }}" class="btn btn-info btn-xs"
                                        title="Voir">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    {{-- @endcan --}}

                                    {{-- @can('role-edit') --}}
                                    <a href="{{ route('roles.edit', $data->ulid) }}" class="btn btn-primary btn-xs"
                                        title="Modifier">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    {{-- @endcan --}}

                                    {{-- @can('role-delete') --}}
                                    <button type="button" class="btn btn-danger delete-btn" title="Supprimer"
                                        data-url="{{ route('roles.destroy', $data->ulid) }}"
                                        data-message="<strong>{{ $data->name }}</strong> sera supprimé. Continuer ?">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    {{-- @endcan --}}
                                </div>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer clearfix">
        <div class="float-right">

        </div>
    </div>
</div>
