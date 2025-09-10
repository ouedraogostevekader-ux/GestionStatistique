<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableauSeeder extends Seeder
{
    public function run(): void
    {
        // Structures (ajoute ça en haut dans ton seeder avant assignations)
        DB::table('structures')->insert([
            [
                'id' => Str::ulid(),
                'nom' => 'Direction Générale',
                'sigle' => 'DG',
                'type_structure' => 'Direction',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::ulid(),
                'nom' => 'Service Informatique',
                'sigle' => 'SI',
                'type_structure' => 'Service',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $structure = DB::table('structures')->first();
        // 1️⃣ Catégories
        DB::table('categories')->insert([
            ['id' => Str::ulid(), 'libelle' => 'Finance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::ulid(), 'libelle' => 'Ressources Humaines', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2️⃣ Tableaux
        DB::table('tableaux')->insert([
            ['id' => Str::ulid(), 'libelle' => 'Budget Annuel', 'description' => 'Suivi du budget', 'categorie_id' => DB::table('categories')->first()->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3️⃣ Règles de gestion
        DB::table('regles_de_gestion')->insert([
            ['id' => Str::ulid(), 'libelle' => 'Règle 1', 'operations' => 'SUM', 'tableau_id' => DB::table('tableaux')->first()->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4️⃣ Assignations
        DB::table('assignations')->insert([
            [
                'id' => Str::ulid(),
                'tableau_id' => DB::table('tableaux')->first()->id,
                'structure_id' => $structure->id,
                'version' => 'v1',
                'source' => 'manuel',
                'responsable' => 'admin',
                'date_debut' => now(),
                'date_fin' => '2025-12-31',
                'annee' => '2025',
                'periode' => 'Annuel',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // 5️⃣ Lignes tableau
        DB::table('lignes_tableau')->insert([
            ['id' => Str::ulid(), 'nom_ligne' => 'Recettes', 'ordre' => '1', 'tableau_id' => DB::table('tableaux')->first()->id, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::ulid(), 'nom_ligne' => 'Dépenses', 'ordre' => '2', 'tableau_id' => DB::table('tableaux')->first()->id, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6️⃣ Colonnes tableau
        DB::table('colonnes_tableau')->insert([
            ['id' => Str::ulid(), 'colonne_ligne' => 'Janvier', 'ordre' => '1', 'tableau_id' => DB::table('tableaux')->first()->id, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::ulid(), 'colonne_ligne' => 'Février', 'ordre' => '2', 'tableau_id' => DB::table('tableaux')->first()->id, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7️⃣ Cellules
        DB::table('cellules')->insert([
            ['id' => Str::ulid(), 'ligne_id' => DB::table('lignes_tableau')->first()->id, 'colonne_id' => DB::table('colonnes_tableau')->first()->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 8️⃣ Soumissions tableau
        DB::table('soumissions_tableau')->insert([
            ['id' => Str::ulid(), 'valeur' => '1000', 'tableau_id' => DB::table('tableaux')->first()->id, 'structure_id' => $structure->id, 'assignation_id' => DB::table('assignations')->first()->id, 'cellule_id' => DB::table('cellules')->first()->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
