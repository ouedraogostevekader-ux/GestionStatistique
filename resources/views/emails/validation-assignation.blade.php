@component('mail::message')
# MINISTÈRE DES INFRASTRUCTURES ET DU DÉSENCLAVEMENT
## DIRECTION GÉNÉRALE DES ÉTUDES ET DES STATISTIQUES SECTORIELLES (DGESS)

Bonjour **{{ $structure->nom ?? 'Structure sectorielle' }}**,

Nous avons le plaisir de vous informer que vos données pour le tableau statistique **« {{ $tableau->libelle ?? 'N/A' }} »** ont été **validées définitivement** par la DGESS.

- **Tableau :** {{ $tableau->libelle ?? 'N/A' }}
- **Campagne / Version :** {{ $version->code ?? '' }} - {{ $version->annee ?? date('Y') }}
- **Date de validation :** {{ now()->format('d/m/Y à H:i') }}
- **Statut :** Validé / Intégré à l'Annuaire officiel

Vos données sont désormais consolidées pour la publication officielle de l'Annuaire Statistique.

@component('mail::button', ['url' => $url, 'color' => 'success'])
Consulter la soumission validée
@endcomponent

La DGESS vous remercie vivement pour votre collaboration et la qualité des informations fournies.

Cordialement,  
**La Direction Générale des Études et des Statistiques Sectorielles (DGESS)**  
*Ministère des Infrastructures et du Désenclavement*
@endcomponent
