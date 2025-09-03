<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programmes = ['Programme_Budgetaire', 'Programme_Activite', 'Rapport_Activite', 'Objectif_Strategique', 'Objectif_Specifique', 'Action', 'Activite', 'Indicateur_Programme', 'Suivi'];
        $parametres = ['Structure'];

        foreach ($programmes as $entite) {
            Permission::create(['name' => 'manage' . $entite, 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-list', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-create', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-edit', 'table' => $entite, 'groupe' => 'Programme']);
            Permission::create(['name' => lcfirst($entite) . '-delete', 'table' => $entite, 'groupe' => 'Programme']);
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
