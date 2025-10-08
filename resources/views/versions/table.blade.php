<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="example2">
            <thead>
                <tr>
                    <th>Ordre</th>
                    <th>Code</th>
                    <th>Libelle</th>
                    <th>Annee</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($versions as $key => $version)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $version->code }} </td>
                        <td>{{ $version->libelle }}</td>
                        <td>{{ $version->annee }}</td>
                        <td>
                            <form action="{{ route('versions.destroy', $version->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    {{-- @can('version-list') --}}
                                    <a href="{{ route('versions.show', $version->id) }}" class="btn btn-info btn-xs"
                                        title="Voir">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    {{-- @endcan --}}
                                    {{-- @can('version-edit') --}}
                                    <a href="{{ route('versions.edit', $version->id) }}" class="btn btn-primary btn-xs"
                                        title="Modifier">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    {{-- @endcan --}}
                                    {{-- @can('version-delete') --}}
                                    <button class="btn btn-outline-danger delete-btn"
                                        data-url="{{ route('versions.destroy', $version->id) }}"
                                        data-message="La version <strong>{{ $version->code }} {{ $version->annee }}</strong> sera supprimé. Continuer ?">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    {{-- @endcan --}}
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
