<?php

namespace Tests\Feature;

use App\Exports\TableauCanevasExport;
use App\Models\Assignation;
use App\Models\Categorie;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\Permission;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Tableau;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TableauCanevasImportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Structure $structure;
    protected Version $version;
    protected Categorie $categorie;
    protected Tableau $tableau;
    protected Assignation $assignation;
    protected LigneTableau $ligneHeader;
    protected LigneTableau $ligneParent;
    protected LigneTableau $ligneEnfant;
    protected ColonneTableau $colParent;
    protected ColonneTableau $colEnfant1;
    protected ColonneTableau $colEnfant2;
    protected Cellule $celluleParentCol1;
    protected Cellule $celluleEnfantCol1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Permissions nécessaires
        $permissions = ['tableau-list', 'soumission-create', 'assignation-list'];
        foreach ($permissions as $p) {
            $perm = Permission::where('name', $p)->first();
            if (!$perm) {
                Permission::create([
                    'name' => $p,
                    'guard_name' => 'web',
                    'table' => 'tableaux',
                    'groupe' => 'Tableau',
                ]);
            }
        }

        // 2. Structure et Utilisateur
        $this->structure = Structure::create([
            'nom' => 'Direction Régionale du Centre ' . uniqid(),
            'sigle' => 'DRC_' . substr(uniqid(), -4),
            'type_structure' => 'Déconcentrée',
        ]);

        $this->user = User::factory()->create([
            'structure_id' => $this->structure->id,
            'nom' => 'Kader',
            'prenom' => 'Steve',
            'matricule' => 'MAT-SPRINT3-' . substr(uniqid(), -5),
        ]);
        $this->user->givePermissionTo($permissions);

        // 3. Version et Catégorie
        $this->version = Version::create([
            'code' => 'V-TEST-' . substr(uniqid(), -4),
            'annee' => 2026,
            'is_current' => true,
        ]);

        $this->categorie = Categorie::create([
            'libelle' => 'Infrastructures Routières ' . uniqid(),
        ]);

        // 4. Tableau avec hiérarchie
        $this->tableau = Tableau::create([
            'libelle' => 'Tableau 1.1 : Réseau routier par région ' . uniqid(),
            'description' => 'Tableau de test sprint 3',
            'categorie_id' => $this->categorie->id,
        ]);

        // Lignes : 1ère ligne d'intitulé, 1 ligne parente, 1 sous-ligne
        $this->ligneHeader = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Régions / Provinces',
            'ordre' => 1,
            'parent_id' => null,
        ]);

        $this->ligneParent = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Région du Centre',
            'ordre' => 2,
            'parent_id' => null,
        ]);

        $this->ligneEnfant = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Kadiogo',
            'ordre' => 1,
            'parent_id' => $this->ligneParent->id,
        ]);

        // Colonnes : 1 colonne parente avec 2 sous-colonnes
        $this->colParent = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'Longueur du réseau (km)',
            'ordre' => 1,
            'parent_id' => null,
        ]);

        $this->colEnfant1 = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'Bitumé',
            'ordre' => 1,
            'parent_id' => $this->colParent->id,
        ]);

        $this->colEnfant2 = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'En terre',
            'ordre' => 2,
            'parent_id' => $this->colParent->id,
        ]);

        // Cellules
        $this->celluleParentCol1 = Cellule::create([
            'ligne_id' => $this->ligneParent->id,
            'colonne_id' => $this->colEnfant1->id,
        ]);

        Cellule::create([
            'ligne_id' => $this->ligneParent->id,
            'colonne_id' => $this->colEnfant2->id,
        ]);

        $this->celluleEnfantCol1 = Cellule::create([
            'ligne_id' => $this->ligneEnfant->id,
            'colonne_id' => $this->colEnfant1->id,
        ]);

        Cellule::create([
            'ligne_id' => $this->ligneEnfant->id,
            'colonne_id' => $this->colEnfant2->id,
        ]);

        // Assignation
        $this->assignation = Assignation::create([
            'tableau_id' => $this->tableau->id,
            'structure_id' => $this->structure->id,
            'version_id' => $this->version->id,
            'statut' => 1, // En cours
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-12-31',
            'annee' => 2026,
        ]);
    }

    /**
     * Test 1 : Téléchargement du canevas Excel pré-formaté
     */
    public function test_telechargement_canevas_excel_genere_fichier_valide(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('tableaux.canevas-excel', [
                'id' => $this->tableau->id,
                'assignation_id' => $this->assignation->id,
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'attachment; filename='));

        // Valider le contenu binaire du classeur généré
        $file = $response->getFile();
        $this->assertNotNull($file);
        $this->assertStringStartsWith("PK", file_get_contents($file->getPathname()));

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheetSaisie = $spreadsheet->getSheetByName('Saisie_Donnees');
        $sheetMeta = $spreadsheet->getSheetByName('_METADATA');

        $this->assertNotNull($sheetSaisie, 'La feuille Saisie_Donnees doit exister.');
        $this->assertNotNull($sheetMeta, 'La feuille _METADATA doit exister.');

        // Vérifier l'identifiant du tableau dans les métadonnées
        $this->assertEquals($this->tableau->id, (string) $sheetMeta->getCell('L1')->getValue());
        $this->assertEquals($this->assignation->id, (string) $sheetMeta->getCell('L2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test 2 : Importation réussie d'un canevas complété avec données numériques valides
     */
    public function test_import_excel_valide_enregistre_soumissions_et_met_a_jour_statut(): void
    {
        // 1. Générer le canevas d'origine
        $export = new TableauCanevasExport($this->tableau, $this->assignation);
        $spreadsheet = $export->buildSpreadsheet();

        $sheetSaisie = $spreadsheet->getSheetByName('Saisie_Donnees');
        $sheetMeta = $spreadsheet->getSheetByName('_METADATA');

        // Récupérer la coordonnée de la cellule liée à celluleParentCol1
        $targetCoord = null;
        for ($r = 2; $r <= $sheetMeta->getHighestRow(); $r++) {
            if ($sheetMeta->getCell([2, $r])->getValue() === $this->celluleParentCol1->id) {
                $targetCoord = (string) $sheetMeta->getCell([1, $r])->getValue();
                break;
            }
        }
        $this->assertNotNull($targetCoord, 'La coordonnée de la cellule doit exister dans _METADATA.');

        // Remplir une valeur numérique (ex: 245.75)
        $sheetSaisie->setCellValue($targetCoord, 245.75);

        // Sauvegarder dans un fichier temporaire
        $tempFile = tempnam(sys_get_temp_dir(), 'upload_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        $uploadedFile = new UploadedFile($tempFile, 'canevas_rempli.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // 2. Poster sur la route d'import
        $response = $this->actingAs($this->user)
            ->post(route('tableaux.import-excel', $this->tableau->id), [
                'excel_file' => $uploadedFile,
                'assignation_id' => $this->assignation->id,
                'ecraser' => 1,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // 3. Vérifier l'insertion dans soumissions_tableau
        $soumission = SoumissionTableau::where('cellule_id', $this->celluleParentCol1->id)
            ->where('assignation_id', $this->assignation->id)
            ->first();

        $this->assertNotNull($soumission, 'La soumission doit être enregistrée en base.');
        $this->assertEquals(245.75, (float) $soumission->valeur);

        // 4. Vérifier la mise à jour automatique du statut vers 2 (Transmis / En attente de validation)
        $assignationFresh = $this->assignation->fresh();
        $this->assertEquals(2, $assignationFresh->statut);
        $this->assertEquals('Transmis / En attente de validation', $assignationFresh->statut_libelle);
        $this->assertTrue($assignationFresh->isSubmitted());

        @unlink($tempFile);
    }

    /**
     * Test 3 : Rejet strict des valeurs non numériques avec rollback intégral
     */
    public function test_rejet_import_avec_valeurs_non_numeriques_et_rollback(): void
    {
        // 1. Générer le canevas d'origine
        $export = new TableauCanevasExport($this->tableau, $this->assignation);
        $spreadsheet = $export->buildSpreadsheet();

        $sheetSaisie = $spreadsheet->getSheetByName('Saisie_Donnees');
        $sheetMeta = $spreadsheet->getSheetByName('_METADATA');

        // Remplir une coordonnée avec du texte alphabétique invalide
        $coordInvalide = (string) $sheetMeta->getCell([1, 2])->getValue();
        $sheetSaisie->setCellValue($coordInvalide, 'INVALIDE_TEXTE');

        // Sauvegarder
        $tempFile = tempnam(sys_get_temp_dir(), 'upload_invalide_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        $uploadedFile = new UploadedFile($tempFile, 'canevas_errone.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // Statut initial
        $this->assertEquals(1, $this->assignation->statut);

        // 2. Poster
        $response = $this->actingAs($this->user)
            ->post(route('tableaux.import-excel', $this->tableau->id), [
                'excel_file' => $uploadedFile,
                'assignation_id' => $this->assignation->id,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // 3. Vérifier qu'AUCUNE soumission n'a été insérée (atomicité)
        $countSoumissions = SoumissionTableau::where('assignation_id', $this->assignation->id)->count();
        $this->assertEquals(0, $countSoumissions, 'Aucune soumission ne doit subsister après une anomalie de typage.');

        // 4. Vérifier que le statut n'a pas été changé à 2
        $this->assertEquals(1, $this->assignation->fresh()->statut);

        @unlink($tempFile);
    }

    /**
     * Test 4 : Rejet d'un fichier appartenant à un autre tableau statistique
     */
    public function test_rejet_fichier_incoherent_ou_d_un_autre_tableau(): void
    {
        // Créer un autre tableau B
        $autreTableau = Tableau::create([
            'libelle' => 'Tableau B : Autre catégorie ' . uniqid(),
            'categorie_id' => $this->categorie->id,
        ]);

        $autreAssignation = Assignation::create([
            'tableau_id' => $autreTableau->id,
            'structure_id' => $this->structure->id,
            'version_id' => $this->version->id,
            'statut' => 1,
            'annee' => 2026,
        ]);

        // Générer le canevas du tableau B
        $export = new TableauCanevasExport($autreTableau, $autreAssignation);
        $spreadsheet = $export->buildSpreadsheet();

        $tempFile = tempnam(sys_get_temp_dir(), 'upload_autre_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        $uploadedFile = new UploadedFile($tempFile, 'canevas_tableau_b.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // Tenter de téléverser sur Tableau A
        $response = $this->actingAs($this->user)
            ->post(route('tableaux.import-excel', $this->tableau->id), [
                'excel_file' => $uploadedFile,
                'assignation_id' => $this->assignation->id,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertTrue(str_contains(session('error'), 'autre tableau statistique'));

        @unlink($tempFile);
    }

    /**
     * Test 5 : Contrôle d'habilitation - Seule la structure assignée (ou admin) peut importer
     */
    public function test_controle_acces_et_habilitation_par_structure(): void
    {
        // Créer une autre structure et un autre utilisateur non-admin
        $autreStructure = Structure::create([
            'nom' => 'Structure Extérieure ' . uniqid(),
            'sigle' => 'SE_' . substr(uniqid(), -4),
            'type_structure' => 'Rattachee',
        ]);

        $autreUser = User::factory()->create([
            'structure_id' => $autreStructure->id,
            'nom' => 'Usurpateur',
            'prenom' => 'Test',
            'matricule' => 'MAT-USURP-' . substr(uniqid(), -4),
        ]);
        $autreUser->givePermissionTo(['soumission-create', 'tableau-list']);

        // Fichier Excel valide généré pour le tableau
        $export = new TableauCanevasExport($this->tableau, $this->assignation);
        $spreadsheet = $export->buildSpreadsheet();
        $tempFile = tempnam(sys_get_temp_dir(), 'fake_auth_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        $uploadedFile = new UploadedFile($tempFile, 'canevas_auth.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // Tenter de soumettre pour l'assignation de la structure A
        $response = $this->actingAs($autreUser)
            ->post(route('tableaux.import-excel', $this->tableau->id), [
                'excel_file' => $uploadedFile,
                'assignation_id' => $this->assignation->id,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertTrue(str_contains(session('error'), 'autorisé'));

        @unlink($tempFile);
    }

    public function test_admin_ou_dgess_peut_acceder_saisie_tableau_assigne_a_autre_structure(): void
    {
        // Récupérer ou créer la structure DGESS
        $structureDgess = Structure::firstOrCreate(
            ['sigle' => 'DGESS'],
            [
                'nom' => 'Direction Générale des Études et des Statistiques Sectorielles',
                'type_structure' => 'Centrale',
            ]
        );

        $adminUser = User::factory()->create([
            'structure_id' => $structureDgess->id,
            'nom' => 'Kader',
            'prenom' => 'Steve Admin',
            'matricule' => 'MAT-ADM-' . substr(uniqid(), -4),
        ]);
        $adminUser->givePermissionTo(['soumission-create', 'tableau-list']);

        // Assigner le rôle Super-Admin s'il existe
        $role = \App\Models\Role::where('name', 'Super-Admin')->first();
        if ($role) {
            $adminUser->assignRole($role);
        }

        // L'assignation actuelle appartient à DRC (une autre structure)
        // L'admin doit pouvoir accéder à la page de saisie avec le paramètre assignation_id
        $response = $this->actingAs($adminUser)
            ->get(route('tableaux.saisie', [
                'id' => $this->tableau->id,
                'assignation_id' => $this->assignation->id,
            ]));

        $response->assertStatus(200);
        $response->assertViewIs('tableaux.saisie');
        $response->assertViewHas('structure', function ($struct) {
            return $struct->id === $this->structure->id;
        });
        $response->assertViewHas('latestAssignation', function ($asgn) {
            return $asgn->id === $this->assignation->id;
        });
    }
}
