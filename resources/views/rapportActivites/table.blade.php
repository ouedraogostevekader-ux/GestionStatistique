<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="example1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Libelle</th>
                    <th>Programme d'activité</th>
                    <th>Periode</th>
                    <th>Année</th>
                    <th>Date</th>
                    <th>Fichier Excel</th>
                    <th>Fichier PDF</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rapportActivites as $key => $data)
                    <tr>
                        <td>{{ $key + 1 }} </td>
                        <td>{{ $data->libelle }}</td>
                        <td>{{ $data->programmeActivite->libelle ?? " Indisponible" }}</td>
                        <td>{{ $data->periode->libelle ?? "Indisponible" }}</td>
                        <td>{{ $data->annee}}</td>
                        <td>{{ $data->date_rapport->format('d/m/Y') }}</td>
                        <td>
                            @if($data->file)
                                <a href="{{ asset('storage/rapports/'.$data->file) }}" target="_blank">Voir le fichier</a>
                            @else
                                Aucun fichier
                            @endif    
                        </td>
                        <td>
                            @if($data->file_pdf)
                                <a href="{{ asset('storage/rapports/'.$data->file_pdf) }}" target="_blank">Voir le fichier</a>
                            @else
                                Aucun fichier
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('rapportActivites.destroy', $data->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    {{-- @can('rapportActivite-list')
                                    <a href="{{ route('rapportActivites.show', $data->id) }}" class="btn btn-info btn-xs"
                                        title="Voir">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @endcan --}}

                                    {{-- @can('rapportActivite-edit')
                                    <a href="{{ route('rapportActivites.edit', $data->id) }}" class="btn btn-primary btn-xs"
                                        title="Modifier">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @endcan --}}

                                    @can('rapportActivite-delete')
                                    <button type="button" class="btn btn-danger delete-btn" title="Supprimer"
                                        data-url="{{ route('rapportActivites.destroy', $data->id) }}"
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
                    <th>Programme d'activité</th>
                    <th>Periode</th>
                    <th>Date</th>
                    <th>Année</th>
                    <th>Fichier Excel</th>
                    <th>Fichier PDF</th>
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
