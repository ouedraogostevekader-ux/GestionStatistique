<dl class="row ">
    <dt class="col-sm-4">Libellé :</dt>
    <dd class="col-sm-8">{{ $objectifStrategique->libelle }}</dd>

    <dt class="col-sm-4">Programme Budgetaire :</dt>
    <dd class="col-sm-8">{{ $objectifStrategique->programmeBudgetaire->libelle ?? 'Indisponible' }}</dd>

    <dt class="col-sm-4">Créé le :</dt>
    <dd class="col-sm-8">{{ $objectifStrategique->created_at->format('d/m/Y à H:i') }}</dd>

    <dt class="col-sm-4">Modifié le :</dt>
    <dd class="col-sm-8">{{ $objectifStrategique->updated_at->format('d/m/Y à H:i') }}</dd>
</dl>
