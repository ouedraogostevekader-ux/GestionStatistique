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
        $structures = [
            ['nom' => 'Agence des Travaux d’Infrastructures du Burkina', 'sigle' => 'AGETIB', 'type_structure' => 'Agence'],
            ['nom' => 'Bureau Central des Marchés Publics', 'sigle' => 'BCMP', 'type_structure' => 'Bureau Central'],
            ['nom' => 'Cabinet du Ministre', 'sigle' => 'CABINET', 'type_structure' => 'Cabinet'],
            ['nom' => 'Direction des Archives et de la Documentation', 'sigle' => 'DAD', 'type_structure' => 'Direction'],
            ['nom' => 'Direction de la Gestion des Finances', 'sigle' => 'DGF', 'type_structure' => 'Direction Générale'],
            ['nom' => 'Direction des Affaires Juridiques et du Contentieux', 'sigle' => 'DAJC', 'type_structure' => 'Direction'],
            ['nom' => 'Direction de la Communication et des Relations Publiques', 'sigle' => 'DCRP', 'type_structure' => 'Direction'],
            ['nom' => 'Direction du Développement Institutionnel et de l’Innovation', 'sigle' => 'DDII', 'type_structure' => 'Direction'],
            ['nom' => 'Direction Générale de l’Entretien Routier', 'sigle' => 'DGER', 'type_structure' => 'Direction Générale'],
            ['nom' => 'Direction Générale des Infrastructures Routières', 'sigle' => 'DGIR', 'type_structure' => 'Direction Générale'],
            ['nom' => 'Direction Générale de la Normalisation et des Études Techniques', 'sigle' => 'DGNET', 'type_structure' => 'Direction Générale'],
            ['nom' => 'Direction Générale du Patrimoine Routier', 'sigle' => 'DGPR', 'type_structure' => 'Direction Générale'],
            ['nom' => 'Direction des Marchés Publics', 'sigle' => 'DMP', 'type_structure' => 'Direction'],
            ['nom' => 'Direction des Ressources Humaines', 'sigle' => 'DRH', 'type_structure' => 'Direction'],
            ['nom' => 'Direction des Systèmes d’Information', 'sigle' => 'DSI', 'type_structure' => 'Direction'],
            ['nom' => 'École Nationale des Travaux Publics', 'sigle' => 'ENTP', 'type_structure' => 'Établissement Public'],
            ['nom' => 'Fonds Spécial Routier du Burkina', 'sigle' => 'FSR-B', 'type_structure' => 'Fonds Public'],
            ['nom' => 'Institut Géographique du Burkina', 'sigle' => 'IGB', 'type_structure' => 'Inspection Générale'],
            ['nom' => 'Institut des Travaux Supérieurs', 'sigle' => 'ITS', 'type_structure' => 'Institut'],
            ['nom' => 'Laboratoire National du Bâtiment et des Travaux Publics', 'sigle' => 'LNBTP', 'type_structure' => 'Laboratoire'],
            ['nom' => 'Pôle des Aménagements Yorgho', 'sigle' => 'PAYO', 'type_structure' => 'Pôle'],
            ['nom' => 'Secrétariat Technique des Travaux HIMO', 'sigle' => 'ST-TRHIMO', 'type_structure' => 'Secrétariat Technique'],
            ['nom' => 'Secrétariat Général', 'sigle' => 'SG', 'type_structure' => 'Secrétariat Général'],
            ['nom' => 'Secrétariat Permanent du Programme d’Infrastructures du Faso', 'sigle' => 'SP-PIFAM', 'type_structure' => 'Secrétariat Permanent'],
        ];

        foreach ($structures as $s) {
            Structure::create([
                'nom' => $s['nom'],
                'sigle' => $s['sigle'],
                'type_structure' => $s['type_structure'],
            ]);
        }

        $structure = Structure::create([
            'nom' => 'Direction Générale des Études et des Statistiques Sectorielles',
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
