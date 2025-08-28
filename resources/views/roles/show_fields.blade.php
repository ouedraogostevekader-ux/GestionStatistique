<!-- Name Field -->
<div class="form-group col-sm-12">
    <label for="name">Nom</label>:
    {{ $role->name }}
</div>

<div class="form-group col-sm-12">
    <strong>Permission:</strong>
    <br />
    @foreach ($rolePermis as $key => $valuess)
        <fieldset class="border">
            <legend>{{ $key }}</legend>
            <div class="row">
                <div class="col-sm-3">
                    Entité
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-2">
                            Voir menu
                        </div>
                        <div class="col-sm-2">
                            Ajouter
                        </div>
                        <div class="col-sm-2">
                            Modifier
                        </div>
                        <div class="col-sm-2">
                            Voir
                        </div>
                        <div class="col-sm-2">
                            supprimer
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($valuess as $key => $values)
                    <div class="col-sm-3">
                        {{ $key }}
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            @php $found = false; @endphp
                            @foreach ($values as $value)
                                @if (str_starts_with($value->name, 'manage'))
                                    @php $found = true; @endphp
                                    <div class="col-sm-2">
                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                            @if (in_array($value->ulid, $rolePermissions)) checked @endif>
                                    </div>
                                @endif
                            @endforeach
                            @if (!$found)
                                <div class="col-sm-2">--</div>
                            @endif

                            @php $found = false; @endphp
                            @foreach ($values as $value)
                                @if (str_ends_with($value->name, 'create'))
                                    @php $found = true; @endphp
                                    <div class="col-sm-2">
                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                            @if (in_array($value->ulid, $rolePermissions)) checked @endif>
                                    </div>
                                @endif
                            @endforeach
                            @if (!$found)
                                <div class="col-sm-2">--</div>
                            @endif

                            @php $found = false; @endphp
                            @foreach ($values as $value)
                                @if (str_ends_with($value->name, 'edit'))
                                    @php $found = true; @endphp
                                    <div class="col-sm-2">
                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                            @if (in_array($value->ulid, $rolePermissions)) checked @endif>
                                    </div>
                                @endif
                            @endforeach
                            @if (!$found)
                                <div class="col-sm-2">--</div>
                            @endif

                            @php $found = false; @endphp
                            @foreach ($values as $value)
                                @if (str_ends_with($value->name, 'list'))
                                    @php $found = true; @endphp
                                    <div class="col-sm-2">
                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                            @if (in_array($value->ulid, $rolePermissions)) checked @endif>
                                    </div>
                                @endif
                            @endforeach
                            @if (!$found)
                                <div class="col-sm-2">--</div>
                            @endif

                            @php $found = false; @endphp
                            @foreach ($values as $value)
                                @if (str_ends_with($value->name, 'delete'))
                                    @php $found = true; @endphp
                                    <div class="col-sm-2">
                                        <input type="checkbox" name="permission[]" value="{{ $value->name }}"
                                            @if (in_array($value->ulid, $rolePermissions)) checked @endif>
                                    </div>
                                @endif
                            @endforeach
                            @if (!$found)
                                <div class="col-sm-2">--</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </fieldset>
        <br>
    @endforeach
</div>
