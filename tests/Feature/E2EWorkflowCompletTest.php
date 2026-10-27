<?php

namespace Tests\Feature;

use App\Mail\RejetAssignationMail;
use App\Mail\ValidationAssignationMail;
use App\Models\Assignation;
use App\Models\Categorie;
use App\Models\Cellule;
use App\Models\ColonneTableau;
use App\Models\LigneTableau;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SoumissionTableau;
use App\Models\Structure;
use App\Models\Tableau;
use App\Models\User;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Test E2E - Scénario Complet de bout en bout de la procédure statistique :
 * 1. Initialisation de la version et assignation à une structure sectorielle
 * 2. Authentification et consultation par l'agent sectoriel
 * 3. Téléchargement du canevas Excel vierge
 * 4. Saisie en ligne des données matricielles et calcul automatique
 * 5. Soumission des données à la DGESS (Statut 2 "Transmis")
 * 6. Connexion de l'analyste DGESS & consultation de la soumission
 * 7. Première instruction : Rejet motivé avec motif d'erreur (Statut 4 "Rejeté")
 * 8. Notification de rejet et ré-ouverture de la saisie pour la structure
 * 9. Correction des données et re-soumission automatique (Statut 2 "Transmis")
 * 10. Décision DGESS : Validation définitive officielle (Statut 3 "Validé")
 * 11. Vérification de l'immuabilité post-validation (tentative de falsification bloquée)
 * 12. Consolidation & Verrouillage officiel de l'Annuaire statistique (is_locked = true)
 * 13. Édition et génération des exports officiels certifiés (PDF, Excel, Word)
 * 14. Consultation des indicateurs et graphiques du Dashboard DGESS
 */
class E2EWorkflowCompletTest extends TestCase
{
    use DatabaseTransactions;

    protected User $dgessUser;
    protected User $sectorUser;
    protected Structure $dgessStructure;
    protected Structure $sectorStructure;
    protected Version $version;
    protected Categorie $categorie;
    protected Tableau $tableau;
    protected Assignation $assignation;
    protected LigneTableau $ligne1;
    protected LigneTableau $ligne2;
    protected ColonneTableau $colonne1;
    protected ColonneTableau $colonne2;
    protected Cellule $cellule11;
    protected Cellule $cellule12;
    protected Cellule $cellule21;
    protected Cellule $cellule22;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        // 1. Définition des permissions requises
        $permissions = [
            'tableau-list',
            'tableau-create',
            'tableau-edit',
            'soumission-create',
            'soumission-edit',
            'assignation-list',
            'assignation-edit',
            'version-list',
            'version-edit',
            'annuaire-list',
            'exportPdf',
            'exportWord',
            'exportExcel',
        ];

        foreach ($permissions as $p) {
            $perm = Permission::where('name', $p)->first();
            if (!$perm) {
                Permission::create([
                    'name' => $p,
                    'guard_name' => 'web',
                    'table' => 'system',
                    'groupe' => 'E2E',
                ]);
            }
        }

        // Rôles
        $roleAdmin = Role::where('name', 'Admin')->first() ?? Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $roleAgent = Role::where('name', 'Agent')->first() ?? Role::create(['name' => 'Agent', 'guard_name' => 'web']);

        // 2. Structures
        $this->dgessStructure = Structure::where('sigle', 'DGESS')->first() ?? Structure::create([
            'nom' => 'Direction Générale des Études et des Statistiques Sectorielles',
            'sigle' => 'DGESS',
            'type_structure' => 'Centrale',
        ]);

        $this->sectorStructure = Structure::create([
            'nom' => 'Direction Régionale des Infrastructures du Centre ' . uniqid(),
            'sigle' => 'DRIC_' . substr(uniqid(), -4),
            'type_structure' => 'Déconcentrée',
        ]);

        // 3. Acteurs du cycle
        $this->dgessUser = User::factory()->create([
            'structure_id' => $this->dgessStructure->id,
            'nom' => 'Traoré',
            'prenom' => 'Responsable DGESS',
            'email' => 'dgess.' . uniqid() . '@mid.gov.bf',
            'matricule' => 'DGS-' . substr(uniqid(), -4),
        ]);
        $this->dgessUser->assignRole('Admin');
        $this->dgessUser->givePermissionTo($permissions);

        $this->sectorUser = User::factory()->create([
            'structure_id' => $this->sectorStructure->id,
            'nom' => 'Sawadogo',
            'prenom' => 'Agent Statistiques DRIC',
            'email' => 'dric.' . uniqid() . '@mid.gov.bf',
            'matricule' => 'DRIC-' . substr(uniqid(), -4),
        ]);
        $this->sectorUser->assignRole('Agent');
        $this->sectorUser->givePermissionTo([
            'tableau-list',
            'soumission-create',
            'soumission-edit',
            'assignation-list',
            'annuaire-list',
        ]);

        // 4. Version Annuaire & Catégorie
        $this->version = Version::create([
            'code' => 'V-E2E-' . substr(uniqid(), -4),
            'libelle' => 'Annuaire Statistique National E2E 2026',
            'annee' => '2026',
            'is_locked' => false,
        ]);

        $this->categorie = Categorie::create([
            'libelle' => 'Réseau Routier Bitumé ' . uniqid(),
        ]);

        // 5. Tableau Matriciel
        $this->tableau = Tableau::create([
            'libelle' => 'Linéaire de routes bitumées par état et région',
            'description' => 'Collecte annuelle auprès des directions régionales',
            'categorie_id' => $this->categorie->id,
            'structure_id' => $this->sectorStructure->id,
            'periodicite' => 'Annuelle',
        ]);

        // 6. Lignes et Colonnes matricielles
        $this->ligne1 = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Routes Nationales (RN)',
            'type_ligne' => 'valeur',
            'ordre' => 1,
        ]);
        $this->ligne2 = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Routes Régionales (RR)',
            'type_ligne' => 'valeur',
            'ordre' => 2,
        ]);

        $this->colonne1 = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'Bon état (km)',
            'ordre' => 1,
        ]);
        $this->colonne2 = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'Mauvais état (km)',
            'ordre' => 2,
        ]);

        // 7. Cellules matricielles
        $this->cellule11 = Cellule::create(['ligne_id' => $this->ligne1->id, 'colonne_id' => $this->colonne1->id]);
        $this->cellule12 = Cellule::create(['ligne_id' => $this->ligne1->id, 'colonne_id' => $this->colonne2->id]);
        $this->cellule21 = Cellule::create(['ligne_id' => $this->ligne2->id, 'colonne_id' => $this->colonne1->id]);
        $this->cellule22 = Cellule::create(['ligne_id' => $this->ligne2->id, 'colonne_id' => $this->colonne2->id]);

        // 8. Assignation officielle du tableau à la structure sectorielle
        $this->assignation = Assignation::create([
            'tableau_id' => $this->tableau->id,
            'structure_id' => $this->sectorStructure->id,
            'version_id' => $this->version->id,
            'date_debut' => Carbon::now()->subDays(5),
            'date_fin' => Carbon::now()->addDays(15),
            'statut' => 0, // 0 = En attente
        ]);
    }

    /**
     * Test E2E du cycle de vie complet de bout en bout
     */
    public function test_cycle_de_vie_e2e_complet_collecte_validation_consolidation_et_export(): void
    {
        // -------------------------------------------------------------
        // ETAPE 1 : L'agent sectoriel consulte son dashboard d'assignations
        // -------------------------------------------------------------
        $response = $this->actingAs($this->sectorUser)
            ->get(route('assignations.dashboard'));
        $response->assertStatus(200);

        // -------------------------------------------------------------
        // ETAPE 2 : L'agent télécharge le canevas Excel officiel
        // -------------------------------------------------------------
        $canevasResponse = $this->actingAs($this->sectorUser)
            ->get(route('tableaux.canevas-excel', [
                'id' => $this->tableau->id,
                'assignation_id' => $this->assignation->id,
            ]));
        $canevasResponse->assertStatus(200);
        $this->assertTrue(
            str_contains($canevasResponse->headers->get('content-type', ''), 'spreadsheetml') ||
            str_contains($canevasResponse->headers->get('content-disposition', ''), 'canevas')
        );

        // -------------------------------------------------------------
        // ETAPE 3 : L'agent sectoriel saisit en ligne les données statistiques
        // -------------------------------------------------------------
        $saisieData = [
            'structure_id' => $this->sectorStructure->id,
            'assignation_id' => $this->assignation->id,
            'donnees' => [
                $this->cellule11->id => 1250,
                $this->cellule12->id => 350,
                $this->cellule21->id => 800,
                $this->cellule22->id => 420,
            ],
            'statut' => 2, // 2 = Transmis / En attente de validation
        ];

        $saveResponse = $this->actingAs($this->sectorUser)
            ->post(route('tableaux.save-donnees', $this->tableau->id), $saisieData);

        $saveResponse->assertRedirect();
        
        // Vérification en base : données enregistrées et statut basculé à Transmis
        $this->assertDatabaseHas('soumissions_tableau', [
            'cellule_id' => $this->cellule11->id,
            'valeur' => 1250,
            'assignation_id' => $this->assignation->id,
        ]);
        $this->assertDatabaseHas('soumissions_tableau', [
            'cellule_id' => $this->cellule22->id,
            'valeur' => 420,
            'assignation_id' => $this->assignation->id,
        ]);

        $this->assignation->refresh();
        $this->assertEquals(2, $this->assignation->statut, "L'assignation doit être au statut 2 (Transmis)");

        // -------------------------------------------------------------
        // ETAPE 4 : L'analyste DGESS instruit la soumission et décide d'un rejet motivé
        // -------------------------------------------------------------
        $motifRejet = "Incohérence constatée : le linéaire mauvais état de la RN dépasse le seuil audité.";
        $rejetResponse = $this->actingAs($this->dgessUser)
            ->post(route('assignations.rejeter', $this->assignation->id), [
                'motif_rejet' => $motifRejet,
            ]);

        $rejetResponse->assertSessionHas('warning');
        $this->assignation->refresh();

        $this->assertEquals(4, $this->assignation->statut, "L'assignation doit être au statut 4 (Rejeté / À corriger)");
        $this->assertEquals($motifRejet, $this->assignation->motif_rejet);
        $this->assertEquals($this->dgessUser->id, $this->assignation->rejete_par);
        Mail::assertSent(RejetAssignationMail::class);

        // -------------------------------------------------------------
        // ETAPE 5 : L'agent sectoriel corrige les données et re-soumet
        // -------------------------------------------------------------
        $correctionData = [
            'structure_id' => $this->sectorStructure->id,
            'assignation_id' => $this->assignation->id,
            'donnees' => [
                $this->cellule11->id => 1400,
                $this->cellule12->id => 200, // Chiffre corrigé
                $this->cellule21->id => 800,
                $this->cellule22->id => 420,
            ],
            'statut' => 2,
        ];

        $reSubmitResponse = $this->actingAs($this->sectorUser)
            ->post(route('tableaux.save-donnees', $this->tableau->id), $correctionData);
        $reSubmitResponse->assertRedirect();

        $this->assignation->refresh();
        $this->assertEquals(2, $this->assignation->statut, "Après correction, le statut doit automatiquement repasser à 2 (Transmis)");
        $this->assertDatabaseHas('soumissions_tableau', [
            'cellule_id' => $this->cellule12->id,
            'valeur' => 200,
        ]);

        // -------------------------------------------------------------
        // ETAPE 6 : La DGESS procède à la Validation Définitive Officielle
        // -------------------------------------------------------------
        $validationResponse = $this->actingAs($this->dgessUser)
            ->post(route('assignations.valider', $this->assignation->id));

        $validationResponse->assertSessionHas('success');
        $this->assignation->refresh();

        $this->assertEquals(3, $this->assignation->statut, "L'assignation doit être au statut 3 (Validé / Terminé)");
        $this->assertNotNull($this->assignation->date_validation);
        $this->assertEquals($this->dgessUser->id, $this->assignation->valide_par);
        Mail::assertSent(ValidationAssignationMail::class);

        // -------------------------------------------------------------
        // ETAPE 7 : Vérification de l'immuabilité (tentative d'altération bloquée)
        // -------------------------------------------------------------
        $alterationAttempt = $this->actingAs($this->sectorUser)
            ->post(route('tableaux.save-donnees', $this->tableau->id), [
                'structure_id' => $this->sectorStructure->id,
                'assignation_id' => $this->assignation->id,
                'donnees' => [
                    $this->cellule11->id => 99999, // Falsification post-validation
                ],
            ]);

        // Doit être rejeté (redirection avec flash error ou 403)
        $alterationAttempt->assertSessionHas('error');
        $this->assertDatabaseMissing('soumissions_tableau', [
            'cellule_id' => $this->cellule11->id,
            'valeur' => 99999,
        ]);

        // -------------------------------------------------------------
        // ETAPE 8 : La DGESS verrouille officiellement la Version de l'Annuaire
        // -------------------------------------------------------------
        $lockResponse = $this->actingAs($this->dgessUser)
            ->post(route('versions.verrouiller', $this->version->id));
        $lockResponse->assertSessionHas('success');

        $this->version->refresh();
        $this->assertTrue($this->version->is_locked, "La version doit être marquée verrouillée");
        $this->assertEquals($this->dgessUser->id, $this->version->locked_by);

        // -------------------------------------------------------------
        // ETAPE 9 : Génération et vérification des exports officiels certifiés
        // -------------------------------------------------------------
        // Export PDF
        $pdfResponse = $this->actingAs($this->dgessUser)
            ->get(route('annuaire.export.pdf', ['version' => $this->version->id]));
        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('%PDF', substr($pdfResponse->getContent(), 0, 10));

        // Export Word
        $wordResponse = $this->actingAs($this->dgessUser)
            ->get(route('annuaire.export.word', ['version' => $this->version->id]));
        $wordResponse->assertStatus(200);

        // Export Excel
        $excelResponse = $this->actingAs($this->dgessUser)
            ->get(route('annuaire.export.excel', ['version' => $this->version->id]));
        $excelResponse->assertStatus(200);

        // -------------------------------------------------------------
        // ETAPE 10 : Consultation du tableau de bord d'avancement DGESS
        // -------------------------------------------------------------
        $dashboardResponse = $this->actingAs($this->dgessUser)
            ->get(route('annuaires.index', ['version' => $this->version->id]));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($this->tableau->libelle);
    }
}
