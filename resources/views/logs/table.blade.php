<div class="table-responsive">
    <table class="display table table-striped table-bordered" id="example2">
        <thead>
            <tr>
                <th>No</th>
                <th wire:click="sortBy('causer_id')">Utilisateur</th>
                <th>Opération</th>
                <th wire:click="sortBy('description')">Proprietes</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $key => $log)
                <tr>
                    <td>{{ $key + 1 }} </td>
                    @if (!empty($log->causer))
                        <td><a href="{{ route('logs.causedBy', $log->causer->id) }}">{{ $log->causer->email ?? 'Email indisponible' }}<a>
                        </td>
                    @else
                        <td>Utilisateur Inconnu</td>
                    @endif
                    <td>{{ $log->description }}</td>
                    <td>
                        @if (!empty($log->subject))
                            @php
                                $modP = null;
                                if (substr($log->subject_type, 4) == 'User') {
                                    $modP = 'User';
                                } else {
                                    $modP = substr($log->subject_type, 11);
                                }
                            @endphp
                            @if ($log->description == 'Modification')
                                Modification {{ $log->log_name }} :
                                @if (isset($log->subject->nom))
                                    <a
                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->nom }}<a>
                                        @elseif(isset($log->subject->libelle))
                                            <a
                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->libelle }}<a>
                                                @elseif(isset($log->subject->name))
                                                    <a
                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->name }}<a>
                                                        @elseif(isset($log->changes['attributes']['titre']))
                                                            <a
                                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['attributes']['titre'] }}<a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->id }}<a>
                                @endif
                                <ul>
                                    @foreach ($log->changes['old'] as $k => $value)
                                        <li>{{ $k }}: </li>
                                    @endforeach
                                </ul>
                            @elseif($log->description == 'Suppression')
                                Suppression {{ $log->log_name }} :
                                @if (isset($log->changes['old']['nom']))
                                    <a
                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['old']['nom'] }}<a>
                                        @elseif(isset($log->changes['old']['libelle']))
                                            <a
                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['old']['libelle'] }}<a>
                                                @elseif(isset($log->changes['old']['name']))
                                                    <a
                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['old']['name'] }}<a>
                                                        @elseif(isset($log->changes['old']['titre']))
                                                            <a
                                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['old']['titre'] }}<a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->id }}<a>
                                @endif
                            @elseif($log->description == 'Creation')
                                Creation {{ $log->log_name }} :
                                @if (isset($log->changes['attributes']['nom']))
                                    <a
                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['attributes']['nom'] }}<a>
                                        @elseif(isset($log->changes['attributes']['libelle']))
                                            <a
                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['attributes']['libelle'] }}<a>
                                                @elseif(isset($log->changes['attributes']['name']))
                                                    <a
                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['attributes']['name'] }}<a>
                                                        @elseif(isset($log->changes['attributes']['titre']))
                                                            <a
                                                                href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->changes['attributes']['titre'] }}<a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('logs.subjectOn', ['id' => $log->subject->id ?? 0, 'model' => $modP]) }}">{{ $log->subject->id }}<a>
                                @endif
                            @elseif($log->log_name == 'Connection')
                                Connexion
                            @elseif($log->log_name == 'Deconnection')
                                Deconnexion
                            @elseif($log->description == 'Tentative échouée')
                                Tentative de connexion
                            @else
                                Proprietes indisponibles
                            @endif
                        @else
                            Proprietes indisponibles
                        @endif
                    </td>
                    <td>{{ $log->created_at }}</td>

                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>No</th>
                <th>Utilisateur</th>
                <th>Opération</th>
                <th>Proprietes</th>
                <th>Date</th>
            </tr>
        </tfoot>
    </table>
    {{-- <div class="card-footer clearfix">
        <div class="float-right">
            {{ $logs->links() }}
        </div>
    </div> --}}

</div>
