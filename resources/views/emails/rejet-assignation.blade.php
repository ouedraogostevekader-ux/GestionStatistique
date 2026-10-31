@component('mail::message')
# MINISTÈRE DES INFRASTRUCTURES ET DU DÉSENCLAVEMENT
## DIRECTION GÉNÉRALE DES ÉTUDES ET DES STATISTIQUES SECTORIELLES (DGESS)

Bonjour **{{ $structure->nom ?? 'Structure sectorielle' }}**,

Votre soumission de données pour le tableau statistique **« {{ $tableau->libelle ?? 'N/A' }} »** a été examinée par les services de la DGESS.

Après revue de cohérence et d'exhaustivité, cette soumission a été **rejetée pour correction**.

### Motif du rejet :
> {{ $motif }}

- **Tableau :** {{ $tableau->libelle ?? 'N/A' }}
- **Campagne / Version :** {{ $version->code ?? '' }} - {{ $version->annee ?? date('Y') }}
- **Date de rejet :** {{ now()->format('d/m/Y à H:i') }}

La saisie pour cette assignation a été ré-ouverte. Nous vous invitons à apporter les corrections demandées et à soumettre de nouveau vos données en ligne ou par import Excel.

@component('mail::button', ['url' => $url, 'color' => 'error'])
Corriger et re-soumettre
@endcomponent

Merci pour votre réactivité et votre engagement pour la qualité des statistiques ministérielles.

Cordialement,  
**La Direction Générale des Études et des Statistiques Sectorielles (DGESS)**  
*Ministère des Infrastructures et du Désenclavement*
@endcomponent
