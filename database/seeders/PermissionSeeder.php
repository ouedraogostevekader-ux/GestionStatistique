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
        $administrations = ['ProgBugetaire', 'ProgActivite', 'RapActivite', 'ObjStrat', 'ObjSpec', 'Action', 'Activite', 'IndicProg', 'Suivi'];
        $parametres = ['Structure'];

        foreach ($administrations as $entite) {
            Permission::create(['name' => 'manage' . $entite, 'table' => $entite, 'groupe' => 'Administration']);
            Permission::create(['name' => lcfirst($entite) . '-list', 'table' => $entite, 'groupe' => 'Administration']);
            Permission::create(['name' => lcfirst($entite) . '-create', 'table' => $entite, 'groupe' => 'Administration']);
            Permission::create(['name' => lcfirst($entite) . '-edit', 'table' => $entite, 'groupe' => 'Administration']);
            Permission::create(['name' => lcfirst($entite) . '-delete', 'table' => $entite, 'groupe' => 'Administration']);
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
