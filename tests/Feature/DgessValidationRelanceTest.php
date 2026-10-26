<?php

namespace Tests\Feature;

use App\Mail\RejetAssignationMail;
use App\Mail\RelanceAssignationMail;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class DgessValidationRelanceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $sectorUser;
    protected Structure $dgessStructure;
    protected Structure $sectorStructure;
    protected Version $version;
    protected Categorie $categorie;
    protected Tableau $tableau;
    protected Assignation $assignation;
    protected LigneTableau $ligne;
    protected ColonneTableau $colonne;
    protected Cellule $cellule;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // 1. Permissions nécessaires
        $permissions = [
            'tableau-list',
            'soumission-create',
            'assignation-list',
            'assignation-edit',
            'version-list',
            'version-edit'
        ];

        foreach ($permissions as $p) {
            $perm = Permission::where('name', $p)->first();
            if (!$perm) {
                Permission::create([
                    'name' => $p,
                    'guard_name' => 'web',
                    'table' => 'assignations',
                    'groupe' => 'Assignation',
                ]);
            }
        }

        // Rôle Admin
        $roleAdmin = Role::where('name', 'Admin')->first();
        if (!$roleAdmin) {
            $roleAdmin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        }

        // 2. Structures
        $this->dgessStructure = Structure::where('sigle', 'DGESS')->first() ?? Structure::create([
            'nom' => 'Direction Générale des Études et des Statistiques Sectorielles',
            'sigle' => 'DGESS',
            'type_structure' => 'Centrale',
        ]);

        $this->sectorStructure = Structure::create([
            'nom' => 'Direction Générale de l\'Entretien Routier ' . uniqid(),
            'sigle' => 'DGER_' . substr(uniqid(), -4),
            'type_structure' => 'Centrale',
        ]);

        // 3. Utilisateurs
        $this->adminUser = User::factory()->create([
            'structure_id' => $this->dgessStructure->id,
            'nom' => 'Kader',
            'prenom' => 'Steve',
            'email' => 'steve.dgess.' . uniqid() . '@mid.gov.bf',
            'matricule' => 'ADM-' . substr(uniqid(), -5),
        ]);
        $this->adminUser->assignRole('Admin');
        $this->adminUser->givePermissionTo($permissions);

        $this->sectorUser = User::factory()->create([
            'structure_id' => $this->sectorStructure->id,
            'nom' => 'Ouédraogo',
            'prenom' => 'Agent',
            'email' => 'agent.dger.' . uniqid() . '@mid.gov.bf',
            'matricule' => 'SEC-' . substr(uniqid(), -5),
        ]);
        $this->sectorUser->givePermissionTo(['tableau-list', 'soumission-create', 'assignation-list']);

        // 4. Version et Tableau
        $this->version = Version::create([
            'code' => 'V-TEST-' . substr(uniqid(), -4),
            'libelle' => 'Édition Test Sprint 4',
            'annee' => '2026',
            'is_locked' => false,
        ]);

        $this->categorie = Categorie::create([
            'libelle' => 'Infrastructures Routières ' . uniqid(),
        ]);

        $this->tableau = Tableau::create([
            'libelle' => 'Réseau Routier Bitumé ' . uniqid(),
            'description' => 'Statistiques réseau bitumé national',
            'categorie_id' => $this->categorie->id,
        ]);

        $this->ligne = LigneTableau::create([
            'tableau_id' => $this->tableau->id,
            'nom_ligne' => 'Routes Nationales',
            'ordre' => 1,
            'parent_id' => null,
        ]);

        $this->colonne = ColonneTableau::create([
            'tableau_id' => $this->tableau->id,
            'colonne_ligne' => 'Linéaire (km)',
            'ordre' => 1,
            'parent_id' => null,
        ]);

        $this->cellule = Cellule::create([
            'id' => (string) Str::ulid(),
            'ligne_id' => $this->ligne->id,
            'colonne_id' => $this->colonne->id,
        ]);

        // 5. Assignation
        $this->assignation = Assignation::create([
            'tableau_id' => $this->tableau->id,
            'structure_id' => $this->sectorStructure->id,
            'version_id' => $this->version->id,
            'statut' => 2, // Transmis / En attente de validation
            'date_debut' => Carbon::now()->subDays(10)->toDateString(),
            'date_fin' => Carbon::now()->subDays(2)->toDateString(), // Dépassée pour tester retard
            'annee' => 2026,
        ]);

        // Soumission initiale
        SoumissionTableau::create([
            'id' => (string) Str::ulid(),
            'tableau_id' => $this->tableau->id,
            'structure_id' => $this->sectorStructure->id,
            'assignation_id' => $this->assignation->id,
            'cellule_id' => $this->cellule->id,
            'valeur' => '1250.75',
        ]);
    }

    /**
     * Test 1 : La DGESS peut valider définitivement une soumission transmise (statut 2 -> 3)
     */
    public function test_dgess_peut_valider_definitivement_soumission_transmise(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('assignations.valider', $this->assignation->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assignation->refresh();
        $this->assertEquals(3, $this->assignation->statut, 'Le statut de l\'assignation doit passer à 3 (Validé / Terminé).');
        $this->assertEquals($this->adminUser->id, $this->assignation->valide_par);
        $this->assertNotNull($this->assignation->date_validation);
        $this->assertTrue($this->assignation->isValidated());

        Mail::assertSent(ValidationAssignationMail::class);
    }

    /**
     * Test 2 : La DGESS peut rejeter avec motif pour correction (statut 2 -> 4)
     */
    public function test_dgess_peut_rejeter_avec_motif_et_notifier_structure(): void
    {
        $motif = "Veuillez vérifier le kilométrage de la RN4 qui semble sous-évalué.";

        $response = $this->actingAs($this->adminUser)
            ->post(route('assignations.rejeter', $this->assignation->id), [
                'motif_rejet' => $motif,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $this->assignation->refresh();
        $this->assertEquals(4, $this->assignation->statut, 'Le statut doit passer à 4 (Rejeté / À corriger).');
        $this->assertEquals($motif, $this->assignation->motif_rejet);
        $this->assertEquals($this->adminUser->id, $this->assignation->rejete_par);
        $this->assertNotNull($this->assignation->date_rejet);
        $this->assertTrue($this->assignation->isRejected());

        Mail::assertSent(RejetAssignationMail::class, function ($mail) use ($motif) {
            return $mail->motif === $motif && $mail->assignation->id === $this->assignation->id;
        });
    }

    /**
     * Test 3 : Le rejet échoue si le motif est absent ou trop court (< 10 caractères)
     */
    public function test_rejet_echoue_si_motif_absent_ou_trop_court(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('assignations.rejeter', $this->assignation->id), [
                'motif_rejet' => 'Invalide', // 8 caractères < 10
            ]);

        $response->assertSessionHasErrors(['motif_rejet']);

        $this->assignation->refresh();
        $this->assertEquals(2, $this->assignation->statut, 'Le statut ne doit pas changer en cas d\'erreur de validation.');
    }

    /**
     * Test 4 : Une soumission corrigée après rejet repasse automatiquement au statut 2
     */
    public function test_re_soumission_apres_rejet_repasse_automatiquement_au_statut_transmis(): void
    {
        // 1. Rejet préalable
        $this->assignation->update([
            'statut' => 4,
            'motif_rejet' => 'Anomalie détectée sur cellule',
        ]);

        // 2. L'agent sectoriel soumet ses corrections
        $response = $this->actingAs($this->sectorUser)
            ->post(route('tableaux.save-donnees', $this->tableau->id), [
                'structure_id' => $this->sectorStructure->id,
                'assignation_id' => $this->assignation->id,
                'donnees' => [
                    $this->cellule->id => '1420.50'
                ]
            ]);

        $response->assertRedirect(route('tableaux.show', $this->tableau->id));

        $this->assignation->refresh();
        $this->assertEquals(2, $this->assignation->statut, 'L\'assignation rejetée doit repasser au statut 2 (Transmis) après correction.');
    }

    /**
     * Test 5 : Un utilisateur non autorisé ne peut ni valider ni rejeter
     */
    public function test_utilisateur_non_autorise_ne_peut_pas_valider_ni_rejeter(): void
    {
        // L'utilisateur sectoriel tente de valider
        $response = $this->actingAs($this->sectorUser)
            ->post(route('assignations.valider', $this->assignation->id));

        $this->assertTrue(in_array($response->status(), [403, 302]));

        // L'utilisateur sectoriel tente de rejeter
        $response = $this->actingAs($this->sectorUser)
            ->post(route('assignations.rejeter', $this->assignation->id), [
                'motif_rejet' => 'Tentative non autorisée',
            ]);

        $this->assertTrue(in_array($response->status(), [403, 302]));

        $this->assignation->refresh();
        $this->assertEquals(2, $this->assignation->statut, 'Le statut de l\'assignation ne doit pas avoir changé.');
    }

    /**
     * Test 6 : Relance manuelle d'une structure retardataire
     */
    public function test_relance_manuelle_structure_retardataire(): void
    {
        $this->assignation->update(['statut' => 1]); // En cours et date_fin dépassée

        $response = $this->actingAs($this->adminUser)
            ->post(route('assignations.relancer', $this->assignation->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assignation->refresh();
        $this->assertEquals(1, $this->assignation->relances_count);
        $this->assertNotNull($this->assignation->derniere_relance_at);

        Mail::assertSent(RelanceAssignationMail::class);
    }

    /**
     * Test 7 : Exécution de la commande Artisan app:relancer-retardataires
     */
    public function test_commande_artisan_relance_retardataires(): void
    {
        $this->assignation->update([
            'statut' => 1,
            'derniere_relance_at' => null,
            'relances_count' => 0
        ]);

        $exitCode = Artisan::call('app:relancer-retardataires', ['--force' => true]);

        $this->assertEquals(0, $exitCode);

        $this->assignation->refresh();
        $this->assertEquals(1, $this->assignation->relances_count);
        $this->assertNotNull($this->assignation->derniere_relance_at);

        Mail::assertSent(RelanceAssignationMail::class);
    }

    /**
     * Test 8 : Verrouillage officiel de l'Annuaire et interdiction stricte de modification
     */
    public function test_verrouillage_officiel_version_annuaire_et_immuabilite(): void
    {
        // 1. Verrouillage par l'administrateur
        $response = $this->actingAs($this->adminUser)
            ->post(route('versions.verrouiller', $this->version->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->version->refresh();
        $this->assertTrue($this->version->isLocked());
        $this->assertNotNull($this->version->date_verrouillage);

        // 2. Tentative de modification des données sur cette version verrouillée
        $responseSaisie = $this->actingAs($this->sectorUser)
            ->post(route('tableaux.save-donnees', $this->tableau->id), [
                'structure_id' => $this->sectorStructure->id,
                'assignation_id' => $this->assignation->id,
                'donnees' => [
                    $this->cellule->id => '9999.99'
                ]
            ]);

        $responseSaisie->assertSessionHas('error');

        // Vérifier que la valeur n'a pas été modifiée
        $soumission = SoumissionTableau::where('assignation_id', $this->assignation->id)
            ->where('cellule_id', $this->cellule->id)
            ->first();

        $this->assertEquals('1250.75', $soumission->valeur);

        // 3. Déverrouillage par l'administrateur
        $responseDeverrouiller = $this->actingAs($this->adminUser)
            ->post(route('versions.deverrouiller', $this->version->id));

        $responseDeverrouiller->assertSessionHas('warning');

        $this->version->refresh();
        $this->assertFalse($this->version->isLocked());
    }
}
