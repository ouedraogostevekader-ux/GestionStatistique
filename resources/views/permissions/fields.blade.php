<div class="row">

    <!-- Name Field -->
    <div class="form-group col-sm-6">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ old('name', $permission->name ?? null) }}">
    </div>

    <div class="form-group col-sm-6">
        <label for="table">Table</label>
        <input type="text" name="table" id="table" class="form-control"
            value="{{ old('table', $permission->table ?? null) }}">
    </div>

    <div class="form-group col-sm-6">
        <label for="groupe">Groupe</label>
        <select name="groupe" id="groupe" class="form-control">
            <option value=""></option>
            @foreach ($groupes as $key => $value)
                <option value="{{ $key }}"
                    {{ old('groupe', $permission->groupe ?? null) == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

</div>
<!-- Submit Field -->
