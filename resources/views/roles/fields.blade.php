<div class="row">

    <!-- Name Field -->
    <div class="form-group col-sm-8">
        <label for="name" class="form-label fw-bold">Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $role->name ?? null) }}">
    </div>

    <div class="form-group col-sm-12">
        <strong>Permission:</strong>
        <br />

        @foreach ($rolePermis as $key => $valuess)
            <fieldset class="border">
                <legend>{{ $key }}</legend>
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Entité</strong>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-2">
                                <strong>Voir menu</strong>
                            </div>
                            <div class="col-sm-2">
                                <strong>Ajouter</strong>
                            </div>
                            <div class="col-sm-2">
                                <strong>Modifier</strong>
                            </div>
                            <div class="col-sm-2">
                                <strong>Voir</strong>
                            </div>
                            <div class="col-sm-2">
                                <strong>supprimer</strong>
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

</div>
