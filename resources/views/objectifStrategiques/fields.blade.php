<div class="row">

    <!-- Libelle Field -->
    <div class="form-group col-sm-8">
        <label for="libelle">Libelle</label>
        <input type="text" name="libelle" id="libelle" class="form-control"
            value="{{ old('libelle', $objectifStrategique->libelle ?? null) }}">
    </div>

    <!-- programme_budgetaire_id field: select -->
    @if (!$programmeBudgetaire)
        <div class="form-group col-sm-8">
            <label for="programme_budgetaire_id">Programme Budgetaire</label>
            <select name="programme_budgetaire_id" id="programme_budgetaire_id" class="form-control">
                <option value="">-- Sélectionner un programme budgetaire --</option>
                @foreach ($programmeBudgetaires as $programmeBudgetaire)
                    <option value="{{ $programmeBudgetaire->id }}"
                        {{ (old('programme_budgetaire_id') ?? ($objectifStrategique->programme_budgetaire_id ?? '')) === $programmeBudgetaire->id ? 'selected' : '' }}>
                        {{ $programmeBudgetaire->libelle ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
