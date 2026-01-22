<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $programmes = ['Programme_Budgetaire', 'Programme_Activite', 'Rapport_Activite', 'Objectif_Strategique', 'Objectif_Specifique', 'Action', 'Activite', 'Indicateur_Programme', 'Suivi', 'Rapport'];
        $tableaux = ['Tableau', 'Ligne_tableau', 'Colonne_tableau', 'Cellule', 'Assignation', 'Soumission', 'Categorie', 'Regle_Gestion', 'Version', 'ExportPdf', 'ExportWord'];
        //$pips = ['Pip', 'Projet', 'Programme_Pip', 'Bailleur', 'Secteur_Planification'];
        $parametres = ['Structure'];
        Permission::create(['name' => 'addLigne-create', 'table' => 'ligne_tableau', 'groupe' => 'Tableau']);
        Permission::create(['name' => 'addLigne-delete', 'table' => 'ligne_tableau', 'groupe' => 'Tableau']);
        Permission::create(['name' => 'addColonne-create', 'table' => 'colonne_tableau', 'groupe' => 'Tableau']);
        Permission::create(['name' => 'addColonne-delete', 'table' => 'colonne_tableau', 'groupe' => 'Tableau']);

        foreach ($programmes as $entite) {
            Permission::create(['name' => 'manage' . $entite, 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-list', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-create', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-edit', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-delete', 'table' => $entite, 'groupe' => 'Programme']);
        }

        foreach ($tableaux as $entite) {
            Permission::create(['name' => 'manage' . $entite, 'table' => $entite, 'groupe' => 'Tableau']);
            Permission::create(['name' => lcfirst($entite) . '-list', 'table' => $entite, 'groupe' => 'Tableau']);
            Permission::create(['name' => lcfirst($entite) . '-create', 'table' => $entite, 'groupe' => 'Tableau']);
            Permission::create(['name' => lcfirst($entite) . '-edit', 'table' => $entite, 'groupe' => 'Tableau']);
            Permission::create(['name' => lcfirst($entite) . '-delete', 'table' => $entite, 'groupe' => 'Tableau']);
        }

        foreach ($parametres as $entite) {
            Permission::create(['name' => 'manage' . $entite, 'table' => $entite, 'groupe' => 'Paramétrage']);
            Permission::create(['name' => lcfirst($entite) . '-list', 'table' => $entite, 'groupe' => 'Paramétrage']);
            Permission::create(['name' => lcfirst($entite) . '-create', 'table' => $entite, 'groupe' => 'Paramétrage']);
            Permission::create(['name' => lcfirst($entite) . '-edit', 'table' => $entite, 'groupe' => 'Paramétrage']);
            Permission::create(['name' => lcfirst($entite) . '-delete', 'table' => $entite, 'groupe' => 'Paramétrage']);
        }
    }
}
