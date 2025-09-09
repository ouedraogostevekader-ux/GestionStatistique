<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tableau;
use App\Models\LigneTableau;
use App\Models\ColonneTableau;
use App\Models\Cellule;
use Illuminate\Support\Str;

class InitTableauCommand extends Command
{
    protected $signature = 'tableau:init {tableau_id} {--lignes=} {--colonnes=}';
    protected $description = 'Initialiser la structure d\'un tableau avec des lignes et colonnes prédéfinies';

    public function handle()
    {
        $tableauId = $this->argument('tableau_id');
        $tableau = Tableau::findOrFail($tableauId);

        $this->info("Initialisation du tableau: {$tableau->libelle}");

        // Créer des lignes par défaut si spécifiées
        if ($this->option('lignes')) {
            $lignes = explode(',', $this->option('lignes'));
            foreach ($lignes as $index => $nomLigne) {
                $ligne = LigneTableau::create([
                    'id' => Str::ulid(),
                    'nom_ligne' => trim($nomLigne),
                    'ordre' => $index + 1,
                    'tableau_id' => $tableauId
                ]);
                $this->info("Ligne créée: {$ligne->nom_ligne}");
            }
        }

        // Créer des colonnes par défaut si spécifiées
        if ($this->option('colonnes')) {
            $colonnes = explode(',', $this->option('colonnes'));
            foreach ($colonnes as $index => $nomColonne) {
                $colonne = ColonneTableau::create([
                    'id' => Str::ulid(),
                    'colonne_ligne' => trim($nomColonne),
                    'ordre' => $index + 1,
                    'tableau_id' => $tableauId
                ]);
                $this->info("Colonne créée: {$colonne->colonne_ligne}");
            }
        }

        // Créer toutes les cellules
        $this->createAllCellules($tableauId);

        $this->info('Tableau initialisé avec succès!');
    }

    private function createAllCellules($tableauId)
    {
        $lignes = LigneTableau::where('tableau_id', $tableauId)->get();
        $colonnes = ColonneTableau::where('tableau_id', $tableauId)->get();

        $cellules_created = 0;
        foreach ($lignes as $ligne) {
            foreach ($colonnes as $colonne) {
                Cellule::create([
                    'id' => Str::ulid(),
                    'ligne_id' => $ligne->id,
                    'colonne_id' => $colonne->id
                ]);
                $cellules_created++;
            }
        }

        $this->info("{$cellules_created} cellules créées");
    }
}