<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        Role::create(['name' => 'Coordonateur']);
        Role::create(['name' => 'Correspondant']);
        Permission::create(['name' => 'manageDeProgramme', 'table' => 'interfaceProgramme', 'groupe' => 'Programme']);
        //        Permission::create(['name' => 'manageRapport', 'table' => 'interfaceRapport', 'groupe' => 'Rapport']);
        //      Permission::create(['name' => 'manageTableau', 'table' => 'interfaceTableau', 'groupe' => 'Annuaire']);
    }
}
