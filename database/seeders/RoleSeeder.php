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
        Role::create(['name' => 'Cordonateur']);
        Role::create(['name' => 'Structure']);
        Permission::create(['name' => 'manageDeProgramme', 'table' => 'interfaceProgramme', 'groupe' => 'Programme']);
        Permission::create(['name' => 'manageRapport', 'table' => 'interfaceRapport', 'groupe' => 'Rapport']);
    }
}
