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

    public function test_hierarchie_sous_lignes_et_sous_colonnes_sans_dedoublement(): void
    {
        // 1. Créer la version
        $version = Version::create([
            'code' => 'V_' . uniqid(),
            'libelle' => 'Version 2024 Hiérarchie',
            'annee' => 2024,
        ]);

        // 2. Créer la structure
        $structure = Structure::create([
            'nom' => 'Direction Infrastructure ' . uniqid(),
            'sigle' => 'DI_' . substr(uniqid(), -4),
            'type_structure' => 'Centrale',
        ]);

        // 3. Créer catégorie et tableau
        $categorie = Categorie::create([
            'libelle' => 'Catégorie Transport ' . uniqid(),
        ]);

        $tableau = Tableau::create([
            'libelle' => 'Tableau Réseau Routier ' . uniqid(),
            'description' => 'Tableau avec hiérarchies de lignes et de colonnes',
            'categorie_id' => $categorie->id,
        ]);

        // 4. Créer les lignes : 1 en-tête, 1 parent avec 2 sous-lignes, 1 racine indépendante
        $ligneHeader = LigneTableau::create([
            'nom_ligne' => 'Indicateurs de transport',
            'ordre' => 1,
            'tableau_id' => $tableau->id,
            'parent_id' => null,
        ]);

        $ligneParent = LigneTableau::create([
            'nom_ligne' => 'Routes classées',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
            'parent_id' => null,
        ]);

        $sousLigne1 = LigneTableau::create([
            'nom_ligne' => 'Routes nationales',
            'ordre' => 1,
            'tableau_id' => $tableau->id,
            'parent_id' => $ligneParent->id,
        ]);

        $sousLigne2 = LigneTableau::create([
            'nom_ligne' => 'Routes régionales',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
            'parent_id' => $ligneParent->id,
        ]);

        $ligneIndep = LigneTableau::create([
            'nom_ligne' => 'Pistes rurales',
            'ordre' => 3,
            'tableau_id' => $tableau->id,
            'parent_id' => null,
        ]);

        // 5. Créer les colonnes : 1 parent avec 2 sous-colonnes, 1 racine indépendante
        $colParent = ColonneTableau::create([
            'colonne_ligne' => 'Année 2023',
            'ordre' => 1,
            'tableau_id' => $tableau->id,
            'parent_id' => null,
        ]);

        $sousCol1 = ColonneTableau::create([
            'colonne_ligne' => 'Bitumé',
            'ordre' => 1,
            'tableau_id' => $tableau->id,
            'parent_id' => $colParent->id,
        ]);

        $sousCol2 = ColonneTableau::create([
            'colonne_ligne' => 'Terre',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
            'parent_id' => $colParent->id,
        ]);

        $colIndep = ColonneTableau::create([
            'colonne_ligne' => 'Année 2024',
            'ordre' => 2,
            'tableau_id' => $tableau->id,
            'parent_id' => null,
        ]);

        // 6. Créer les cellules pour toutes les feuilles
        // Sous-ligne 1 (Routes nationales)
        $cell_sn_sc1 = Cellule::create(['ligne_id' => $sousLigne1->id, 'colonne_id' => $sousCol1->id]);
        $cell_sn_sc2 = Cellule::create(['ligne_id' => $sousLigne1->id, 'colonne_id' => $sousCol2->id]);
        $cell_sn_ci  = Cellule::create(['ligne_id' => $sousLigne1->id, 'colonne_id' => $colIndep->id]);

        // Sous-ligne 2 (Routes régionales)
        $cell_sr_sc1 = Cellule::create(['ligne_id' => $sousLigne2->id, 'colonne_id' => $sousCol1->id]);
        $cell_sr_sc2 = Cellule::create(['ligne_id' => $sousLigne2->id, 'colonne_id' => $sousCol2->id]);
        $cell_sr_ci  = Cellule::create(['ligne_id' => $sousLigne2->id, 'colonne_id' => $colIndep->id]);

        // Ligne indépendante (Pistes rurales)
        $cell_pi_sc1 = Cellule::create(['ligne_id' => $ligneIndep->id, 'colonne_id' => $sousCol1->id]);
        $cell_pi_sc2 = Cellule::create(['ligne_id' => $ligneIndep->id, 'colonne_id' => $sousCol2->id]);
        $cell_pi_ci  = Cellule::create(['ligne_id' => $ligneIndep->id, 'colonne_id' => $colIndep->id]);

        // 7. Assigner le tableau
        $assignation = Assignation::create([
            'tableau_id' => $tableau->id,
            'structure_id' => $structure->id,
            'version_id' => $version->id,
            'statut' => 2,
            'annee' => 2024,
        ]);

        // 8. Enregistrer les soumissions
        // Routes nationales : 100 (Bitumé) + 200 (Terre) + 300 (2024) = 600
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sn_sc1->id, 'valeur' => '100']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sn_sc2->id, 'valeur' => '200']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sn_ci->id, 'valeur' => '300']);

        // Routes régionales : 40 (Bitumé) + 60 (Terre) + 50 (2024) = 150
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sr_sc1->id, 'valeur' => '40']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sr_sc2->id, 'valeur' => '60']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_sr_ci->id, 'valeur' => '50']);

        // Pistes rurales : 10 (Bitumé) + 90 (Terre) + 100 (2024) = 200
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_pi_sc1->id, 'valeur' => '10']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_pi_sc2->id, 'valeur' => '90']);
        SoumissionTableau::create(['tableau_id' => $tableau->id, 'structure_id' => $structure->id, 'assignation_id' => $assignation->id, 'cellule_id' => $cell_pi_ci->id, 'valeur' => '100']);

        // 9. Exécuter AnnuaireService
        $service = app(AnnuaireService::class);
        $donnees = $service->getDonneesAnnuaire($version->id);

        $tableauCharge = $donnees['tableaux']->firstWhere('id', $tableau->id);
        $this->assertNotNull($tableauCharge);

        // A. Vérification de l'élimination du dédoublement dans l'arborescence
        // Seules les 3 lignes racines (Indicateurs, Routes classées, Pistes rurales) doivent être au 1er niveau
        $this->assertCount(3, $tableauCharge->lignesTableau);
        $this->assertTrue($tableauCharge->lignesTableau->every(fn($l) => is_null($l->parent_id)));

        // Les sous-lignes ne doivent exister QUE dans les enfants de leur parent
        $parentRowCharge = $tableauCharge->lignesTableau->firstWhere('id', $ligneParent->id);
        $this->assertNotNull($parentRowCharge);
        $this->assertCount(2, $parentRowCharge->enfants);
        $this->assertEquals(['Routes nationales', 'Routes régionales'], $parentRowCharge->enfants->pluck('nom_ligne')->all());

        // Seules les 2 colonnes racines (Année 2023, Année 2024) doivent être au 1er niveau
        $this->assertCount(2, $tableauCharge->colonnesTableau);
        $this->assertTrue($tableauCharge->colonnesTableau->every(fn($c) => is_null($c->parent_id)));

        // Les sous-colonnes ne doivent exister QUE dans les enfants de leur parent
        $parentColCharge = $tableauCharge->colonnesTableau->firstWhere('id', $colParent->id);
        $this->assertNotNull($parentColCharge);
        $this->assertCount(2, $parentColCharge->enfants);
        $this->assertEquals(['Bitumé', 'Terre'], $parentColCharge->enfants->pluck('colonne_ligne')->all());

        // B. Vérification des totaux par ligne
        // Routes nationales : 100 + 200 + 300 = 600
        $this->assertEquals(600.0, $donnees['totauxLignes'][$tableau->id][$sousLigne1->id]);
        // Routes régionales : 40 + 60 + 50 = 150
        $this->assertEquals(150.0, $donnees['totauxLignes'][$tableau->id][$sousLigne2->id]);
        // Routes classées (parent agrégé) : 600 + 150 = 750
        $this->assertEquals(750.0, $donnees['totauxLignes'][$tableau->id][$ligneParent->id]);
        // Pistes rurales : 10 + 90 + 100 = 200
        $this->assertEquals(200.0, $donnees['totauxLignes'][$tableau->id][$ligneIndep->id]);

        // C. Vérification des cellules agrégées sur la ligne parente
        $this->assertEquals(140.0, $donnees['matrice'][$tableau->id][$ligneParent->id][$sousCol1->id]); // 100 + 40
        $this->assertEquals(260.0, $donnees['matrice'][$tableau->id][$ligneParent->id][$sousCol2->id]); // 200 + 60
        $this->assertEquals(350.0, $donnees['matrice'][$tableau->id][$ligneParent->id][$colIndep->id]);  // 300 + 50

        // D. Vérification des totaux par colonne (aucun double comptage)
        // Bitumé : 100 (nationales) + 40 (régionales) + 10 (pistes) = 150
        $this->assertEquals(150.0, $donnees['totauxColonnes'][$tableau->id][$sousCol1->id]);
        // Terre : 200 (nationales) + 60 (régionales) + 90 (pistes) = 350
        $this->assertEquals(350.0, $donnees['totauxColonnes'][$tableau->id][$sousCol2->id]);
        // Année 2024 : 300 (nationales) + 50 (régionales) + 100 (pistes) = 450
        $this->assertEquals(450.0, $donnees['totauxColonnes'][$tableau->id][$colIndep->id]);

        // E. Vérification du Total Général (aucun double comptage)
        // 150 + 350 + 450 = 950 (également 750 + 200 = 950)
        $this->assertEquals(950.0, $donnees['totalGeneral'][$tableau->id]);

        // F. Vérification du rendu HTML Blade (aucun dédoublement visuel)
        $view = view('annuaires.table', [
            'tableau' => $tableauCharge,
            'matrice' => $donnees['matrice'],
            'totauxLignes' => $donnees['totauxLignes'],
            'totauxColonnes' => $donnees['totauxColonnes'],
            'totalGeneral' => $donnees['totalGeneral'],
        ])->render();

        // Le nom de chaque sous-ligne doit apparaître STRICTEMENT UNE SEULE FOIS dans le HTML
        $this->assertEquals(1, substr_count($view, 'Routes nationales'), 'La sous-ligne Routes nationales ne doit apparaître qu’une seule fois.');
        $this->assertEquals(1, substr_count($view, 'Routes régionales'), 'La sous-ligne Routes régionales ne doit apparaître qu’une seule fois.');

        // Le nom de chaque sous-colonne doit apparaître STRICTEMENT UNE SEULE FOIS dans l’en-tête du tableau
        $this->assertEquals(1, substr_count($view, 'Bitumé'), 'La sous-colonne Bitumé ne doit apparaître qu’une seule fois dans l’en-tête.');
        $this->assertEquals(1, substr_count($view, 'Terre'), 'La sous-colonne Terre ne doit apparaître qu’une seule fois dans l’en-tête.');

        // L’en-tête de la ligne ordre=1 est bien utilisé comme titre de la colonne libellé
        $this->assertStringContainsString('Indicateurs de transport', $view);
    }
}
