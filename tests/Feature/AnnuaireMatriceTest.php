<?php

namespace Tests\Feature;

use App\Models\Assignation;
use App\Models\Categorie;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Tableau;
use App\Models\User;
use App\Models\Version;
use App\Services\AnnuaireService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnnuaireMatriceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_annuaire_instanciation_et_structure_retournee(): void
    {
        $service = app(AnnuaireService::class);
        $donnees = $service->getDonneesAnnuaire('inexistant-id');

        $this->assertArrayHasKey('versions', $donnees);
        $this->assertArrayHasKey('tableaux', $donnees);
        $this->assertArrayHasKey('matrice', $donnees);
        $this->assertArrayHasKey('totauxLignes', $donnees);
        $this->assertArrayHasKey('totauxColonnes', $donnees);
        $this->assertArrayHasKey('totalGeneral', $donnees);
    }

    public function test_chargement_matriciel_et_calcul_des_totaux(): void
    {
        // 1. Créer la version
        $version = Version::create([
            'code' => 'V_' . uniqid(),
            'libelle' => 'Version 2024 Test',
            'annee' => 2024,
        ]);

        // 2. Créer la structure
        $structure = Structure::create([
            'nom' => 'Direction Test ' . uniqid(),
            'sigle' => 'DT_' . substr(uniqid(), -4),
            'type_structure' => 'Centrale',
        ]);

        // 3. Créer la catégorie et le tableau
        $categorie = Categorie::create([
            'libelle' => 'Catégorie Test ' . uniqid(),
        ]);

        $tableau = Tableau::create([
            'libelle' => 'Tableau Statistique Test ' . uniqid(),
            'categorie_id' => $categorie->id,
        ]);

        // 4. Créer les lignes et colonnes
        $ligne1 = LigneTableau::create([
            'nom_ligne' => 'Ligne 1',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
        ]);

        $ligne2 = LigneTableau::create([
            'nom_ligne' => 'Ligne 2',
            'ordre' => 3,
            'tableau_id' => $tableau->id,
        ]);

        $colonne1 = ColonneTableau::create([
            'colonne_ligne' => 'Col 1',
            'ordre' => 1,
            'tableau_id' => $tableau->id,
        ]);

        $colonne2 = ColonneTableau::create([
            'colonne_ligne' => 'Col 2',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
        ]);

        // 5. Créer les cellules
        $cell1_1 = Cellule::create(['ligne_id' => $ligne1->id, 'colonne_id' => $colonne1->id]);
        $cell1_2 = Cellule::create(['ligne_id' => $ligne1->id, 'colonne_id' => $colonne2->id]);
        $cell2_1 = Cellule::create(['ligne_id' => $ligne2->id, 'colonne_id' => $colonne1->id]);
        $cell2_2 = Cellule::create(['ligne_id' => $ligne2->id, 'colonne_id' => $colonne2->id]);

        // 6. Assigner le tableau
        $assignation = Assignation::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'version_id' => $version->id,
            'statut' => 2,
            'annee' => 2024,
        ]);

        // 7. Créer les soumissions (valeurs numériques)
        // Ligne 1 : 10 + 20 = 30
        SoumissionTableau::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'assignation_id' => $assignation->id,
            'cellule_id' => $cell1_1->id,
            'valeur' => '10',
        ]);
        SoumissionTableau::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'assignation_id' => $assignation->id,
            'cellule_id' => $cell1_2->id,
            'valeur' => '20',
        ]);

        // Ligne 2 : 30 + 40 = 70
        SoumissionTableau::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'assignation_id' => $assignation->id,
            'cellule_id' => $cell2_1->id,
            'valeur' => '30',
        ]);
        SoumissionTableau::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'assignation_id' => $assignation->id,
            'cellule_id' => $cell2_2->id,
            'valeur' => '40',
        ]);

        // 8. Exécuter le service
        $service = app(AnnuaireService::class);
        $donnees = $service->getDonneesAnnuaire($version->id);

        // Vérifications de la matrice
        $this->assertEquals('10', $donnees['matrice'][$tableau->id][$ligne1->id][$colonne1->id]);
        $this->assertEquals('20', $donnees['matrice'][$tableau->id][$ligne1->id][$colonne2->id]);
        $this->assertEquals('30', $donnees['matrice'][$tableau->id][$ligne2->id][$colonne1->id]);
        $this->assertEquals('40', $donnees['matrice'][$tableau->id][$ligne2->id][$colonne2->id]);

        // Vérifications des totaux calculés
        // Ligne 1 : 10 + 20 = 30
        $this->assertEquals(30.0, $donnees['totauxLignes'][$tableau->id][$ligne1->id]);
        // Ligne 2 : 30 + 40 = 70
        $this->assertEquals(70.0, $donnees['totauxLignes'][$tableau->id][$ligne2->id]);

        // Colonne 1 : 10 + 30 = 40
        $this->assertEquals(40.0, $donnees['totauxColonnes'][$tableau->id][$colonne1->id]);
        // Colonne 2 : 20 + 40 = 60
        $this->assertEquals(60.0, $donnees['totauxColonnes'][$tableau->id][$colonne2->id]);

        // Total Général : 30 + 70 = 100
        $this->assertEquals(100.0, $donnees['totalGeneral'][$tableau->id]);
    }
}
