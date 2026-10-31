@component('mail::message')
# MINISTÈRE DES INFRASTRUCTURES ET DU DÉSENCLAVEMENT
## DIRECTION GÉNÉRALE DES ÉTUDES ET DES STATISTIQUES SECTORIELLES (DGESS)

Bonjour **{{ $structure->nom ?? 'Structure sectorielle' }}**,

@if ($typeRelance === 'echeance_proche')
Nous vous rappelons que la date d'échéance de soumission des données pour le tableau statistique suivant approche à grands pas :
@else
Nous constatons que la date limite de soumission des données pour le tableau statistique suivant est **dépassée** et que vos données n'ont pas encore été transmises ou validées :
@endif

- **Tableau :** {{ $tableau->libelle ?? 'N/A' }}
- **Catégorie :** {{ $tableau->categorie->libelle ?? 'Général' }}
- **Campagne / Version :** {{ $version->code ?? '' }} - {{ $version->annee ?? date('Y') }}
- **Date limite initiale :** {{ $assignation->date_fin ? \Carbon\Carbon::parse($assignation->date_fin)->format('d/m/Y') : 'Non définie' }}
- **Statut actuel :** {{ $assignation->statut_libelle }}

Afin de garantir la production dans les délais de l'**Annuaire Statistique National**, nous vous prions de bien vouloir renseigner vos données ou importer votre canevas Excel dûment rempli dans les meilleurs délais.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Accéder à la saisie du tableau
@endcomponent

En vous remerciant pour votre collaboration à la fiabilité des statistiques du département.

Cordialement,  
**La Direction Générale des Études et des Statistiques Sectorielles (DGESS)**  
*Ministère des Infrastructures et du Désenclavement*
@endcomponent
