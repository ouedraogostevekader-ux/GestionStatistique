<!-- Name Field -->
<div class="form-group col-sm-12">
    <label for="libelle">Libelle</label>:
    {{ $objectifStrategique->libelle }}
</div>
<!-- Programme Budgetaire Id Field -->
<div class="form-group col-sm-12">
    <label for="programme_budgetaire_id">Programme Budgetaire</label>:
    {{ $objectifStrategique->programmeBudgetaire->libelle ?? "Indisponible" }}
</div>
