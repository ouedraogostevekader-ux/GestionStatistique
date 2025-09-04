<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="example1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Libelle</th>
                    <th>Programme d'activité</th>
                    <th>Periode</th>
                    <th>Date</th>
                    <th>Fichier</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($objectifStrategiques as $key => $data)
                    <tr>
                        <td>{{ $key + 1 }} </td>
                        <td>{{ $data->libelle }}</td>
                        <td>{{ $data->programmeBudgetaire->libelle ?? " Indisponible" }}</td>
                        <td>
                            <form action="{{ route('objectifStrategiques.destroy', $data->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    @can('objectifStrategique-list')
                                    <a href="{{ route('objectifStrategiques.show', $data->id) }}" class="btn btn-info btn-xs"
                                        title="Voir">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @endcan

                                    @can('objectifStrategique-edit')
                                    <a href="{{ route('objectifStrategiques.edit', $data->id) }}" class="btn btn-primary btn-xs"
                                        title="Modifier">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @endcan

                                    @can('objectifStrategique-delete')
                                    <button type="button" class="btn btn-danger delete-btn" title="Supprimer"
                                        data-url="{{ route('objectifStrategiques.destroy', $data->id) }}"
                                        data-message="<strong>{{ $data->libelle }}</strong> sera supprimé. Continuer ?">
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
                    <th>Libelle</th>
                    <th>Programme Budgetaire</th>
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
