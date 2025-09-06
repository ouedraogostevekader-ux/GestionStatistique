<?php

namespace Database\Seeders;

use App\Models\Structure;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $structure = Structure::create([
            'nom' => 'DGESS',
            'sigle' => 'DGESS',
            'type_structure' => 'Direction generale'
        ]);
        $user = User::create([
            'name' => 'admin',
            'nom' => 'admin',
            'prenom' => 'admin',
            'telephone' => '77000000',
            'matricule' => 'K983',
            'fonction' => 'admin',
            'email' => 'admin@admin.bf',
            'password' => Hash::make('password'),
            'structure_id' => $structure->id
        ]);

        $user->assignRole('Admin');

        $user->assignRole('Super-Admin');
        $user1 = User::create([
            'name' => 'Admin DGESS',
            'nom' => 'DSI',
            'prenom' => 'MI',
            'email' => 'admin.dgess@example.com',
            'telephone' => '76554411',
            'fonction' => 'Directeur',
            'matricule' => '522722M',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'structure_id' => $structure->id,
        ]);
        $user1->assignRole('Admin');
    }
}
