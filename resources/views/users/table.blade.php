<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="example2">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Nom</th>
                    <th>Telephone</th>
                    <th>Structure</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->name }} </td>
                        <td>{{ $user->telephone }}</td>
                        <td>{{ $user->structure->sigle }}</td>
                        <td>{{ $user->roleLabels() }}</td>
                        <td>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    @can('user-list')
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-xs"
                                            title="Voir">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    @endcan
                                    @can('user-edit')
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-xs"
                                            title="Modifier">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    @endcan
                                    @can('user-delete')
                                        <button class="btn btn-outline-danger delete-btn"
                                            data-url="{{ route('users.destroy', $user->id) }}"
                                            data-message="L'utilisateur <strong>{{ $user->nom }} {{ $user->prenom }}</strong> sera supprimé. Continuer ?">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @endcan
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            {{-- @include('adminlte-templates::common.paginate', ['records' => $users]) --}}
        </div>
    </div>
</div>
