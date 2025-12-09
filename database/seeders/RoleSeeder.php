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
        Role::create(['name' => 'Correspondant']);
        Role::create(['name' => 'Tableau']);
        Permission::create(['name' => 'manageDeProgramme', 'table' => 'interfaceProgramme', 'groupe' => 'Programme']);
        Permission::create(['name' => 'manageDeTableau', 'table' => 'interfaceTableau', 'groupe' => 'Tableau']);
    }
}
