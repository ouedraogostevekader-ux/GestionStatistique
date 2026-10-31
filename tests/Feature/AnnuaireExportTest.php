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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Permission;
use Tests\TestCase;

class AnnuaireExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Version $version;
    protected Categorie $catRoutes;
    protected Categorie $catPistes;
    protected Tableau $tableauRoutes;
    protected Tableau $tableauPistes;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les permissions nécessaires si absentes
        $permissions = ['annuaire-list', 'exportPdf', 'exportWord', 'exportExcel'];
        foreach ($permissions as $p) {
            $perm = Permission::where('name', $p)->first();
            if (!$perm) {
                Permission::create([
                    'name' => $p,
                    'guard_name' => 'web',
                    'table' => 'annuaires',
                    'groupe' => 'Tableau',
                ]);
            }
        }

        // Créer la structure et l'utilisateur authentifié
        $structure = Structure::create([
            'nom' => 'DGESS MID ' . uniqid(),
            'sigle' => 'DGESS_' . substr(uniqid(), -4),
            'type_structure' => 'Centrale',
        ]);

        $this->user = User::factory()->create([
            'structure_id' => $structure->id,
            'nom' => 'Kader',
            'prenom' => 'Steve',
            'matricule' => 'MAT-TEST-' . substr(uniqid(), -6),
        ]);
        $this->user->givePermissionTo($permissions);

        // Créer la version d'essai
        $this->version = Version::create([
            'code' => 'V_EXP_' . uniqid(),
            'libelle' => 'Version Test Export',
            'annee' => 2026,
        ]);

        // Créer 2 catégories
        $this->catRoutes = Categorie::create(['libelle' => 'Réseau Routier ' . uniqid()]);
        $this->catPistes = Categorie::create(['libelle' => 'Pistes Rurales ' . uniqid()]);

        // Créer 2 tableaux
        $this->tableauRoutes = Tableau::create([
            'libelle' => 'Routes Classées Bitumées',
            'categorie_id' => $this->catRoutes->id,
        ]);

        $this->tableauPistes = Tableau::create([
            'libelle' => 'Pistes Rurales Aménagées',
            'categorie_id' => $this->catPistes->id,
        ]);

        // Assigner les 2 tableaux
        $assignationRoutes = Assignation::create([
            'version_id' => $this->version->id,
            'tableau_id' => $this->tableauRoutes->id,
            'structure_id' => $structure->id,
            'user_id' => $this->user->id,
            'date_debut' => now(),
            'date_fin' => now()->addDays(30),
            'statut' => 'Valide',
        ]);

        $assignationPistes = Assignation::create([
            'version_id' => $this->version->id,
            'tableau_id' => $this->tableauPistes->id,
            'structure_id' => $structure->id,
            'user_id' => $this->user->id,
            'date_debut' => now(),
            'date_fin' => now()->addDays(30),
            'statut' => 'Valide',
        ]);

        // Lignes et colonnes pour tableauRoutes
        $header = LigneTableau::create([
            'nom_ligne' => 'Indicateur',
            'ordre' => 1,
            'tableau_id' => $this->tableauRoutes->id,
        ]);

        $ligne1 = LigneTableau::create([
            'nom_ligne' => 'RN1 Ouaga-Bobo',
            'ordre' => 2,
            'tableau_id' => $this->tableauRoutes->id,
        ]);

        $col1 = ColonneTableau::create([
            'colonne_ligne' => 'Longueur (km)',
            'ordre' => 1,
            'tableau_id' => $this->tableauRoutes->id,
        ]);

        $cellule = Cellule::create([
            'tableau_id' => $this->tableauRoutes->id,
            'ligne_id' => $ligne1->id,
            'colonne_id' => $col1->id,
        ]);

        SoumissionTableau::create([
            'assignation_id' => $assignationRoutes->id,
            'tableau_id' => $this->tableauRoutes->id,
            'structure_id' => $structure->id,
            'cellule_id' => $cellule->id,
            'valeur' => '360',
        ]);
    }

    public function test_annuaire_web_index_avec_filtres_categories(): void
    {
        // 1. Accès sans filtre : voit les deux tableaux
        $response = $this->actingAs($this->user)->get('/admin/annuaires?version=' . $this->version->id);
        $response->assertOk();
        $response->assertSee('Routes Classées Bitumées');
        $response->assertSee('Pistes Rurales Aménagées');
        $response->assertSee('Exporter Excel (.xlsx)');
        $response->assertSee('Exporter PDF');
        $response->assertSee('Exporter Word (.docx)');

        // 2. Filtre par Catégorie Routes : voit seulement Routes
        $responseRoutes = $this->actingAs($this->user)->get('/admin/annuaires?version=' . $this->version->id . '&categorie=' . $this->catRoutes->id);
        $responseRoutes->assertOk();
        $responseRoutes->assertSee('Routes Classées Bitumées');
        $responseRoutes->assertDontSee('Pistes Rurales Aménagées');

        // 3. Filtre par Catégorie Pistes : voit seulement Pistes
        $responsePistes = $this->actingAs($this->user)->get('/admin/annuaires?version=' . $this->version->id . '&categorie=' . $this->catPistes->id);
        $responsePistes->assertOk();
        $responsePistes->assertSee('Pistes Rurales Aménagées');
        $responsePistes->assertDontSee('Routes Classées Bitumées');
    }

    public function test_export_pdf_genere_fichier_binaire_valide(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/annuaire/export/pdf?version=' . $this->version->id);

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Annuaire-2026.pdf', $contentDisposition);

        // Vérifier que le contenu est un vrai fichier PDF
        $binary = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $binary);
    }

    public function test_export_excel_genere_classeur_xlsx_valide(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/annuaire/export/excel?version=' . $this->version->id);

        $response->assertOk();
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Annuaire-2026.xlsx', $contentDisposition);

        // Vérifier que le fichier OpenXML .xlsx est généré avec la signature zip "PK"
        $file = $response->getFile();
        $this->assertNotNull($file);
        $binary = file_get_contents($file->getPathname());
        $this->assertStringStartsWith("PK", $binary);
    }

    public function test_export_word_genere_document_docx(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/annuaire/export/word?version=' . $this->version->id);

        $response->assertOk();
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Annuaire-2026.docx', $contentDisposition);

        // Fichier OpenXML Word (.docx) commence également par la signature zip "PK"
        $file = $response->getFile();
        $this->assertNotNull($file);
        $binary = file_get_contents($file->getPathname());
        $this->assertStringStartsWith("PK", $binary);
    }

    public function test_export_redirection_si_version_invalide(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/annuaire/export/pdf?version=inexistante');
        $response->assertRedirect(route('annuaires.index'));
        $response->assertSessionHas('error');

        $responseExcel = $this->actingAs($this->user)->get('/admin/annuaire/export/excel?version=inexistante');
        $responseExcel->assertRedirect(route('annuaires.index'));
        $responseExcel->assertSessionHas('error');

        $responseWord = $this->actingAs($this->user)->get('/admin/annuaire/export/word?version=inexistante');
        $responseWord->assertRedirect(route('annuaires.index'));
        $responseWord->assertSessionHas('error');
    }
}
