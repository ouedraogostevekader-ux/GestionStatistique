<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\User as User;
use App\Models\Structure;
use Illuminate\Support\Facades\Hash;


class DataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Désactiver les contraintes de clés étrangères temporairement
        //DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vider les tables dans l'ordre inverse des dépendances
        $tables = [
            'suivis',
            'indicateurs_programmes',
            'activites',
            'objectifs_specifiques',
            'actions',
            'responsables',
            'programme_budgetaire_structure',
            'rapports_activites',
            'objectifs_strategiques',
            'programmes_budgetaires',
            'programmes_activites',
            'structures'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // Réactiver les contraintes
        //DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Structures
        $structures = [];
        $typesStructures = ['Ministère', 'Direction', 'Service', 'Département', 'Agence'];
        $nomsStructures = [
            ['nom' => 'Ministère de la Santé Publique', 'sigle' => 'MSP'],
            ['nom' => 'Ministère de l\'Éducation Nationale', 'sigle' => 'MENA'],
            ['nom' => 'Ministère des Infrastructures', 'sigle' => 'MI'],
            ['nom' => 'Direction Générale du Budget', 'sigle' => 'DGB'],
            ['nom' => 'Direction des Ressources Humaines', 'sigle' => 'DRH'],
            ['nom' => 'Service de la Planification', 'sigle' => 'SP'],
            ['nom' => 'Agence Nationale de Développement', 'sigle' => 'AND'],
            ['nom' => 'Département de la Communication', 'sigle' => 'DCOM']
        ];

        foreach ($nomsStructures as $index => $struct) {
            $structureId = (string) Str::ulid();
            $structures[] = $structureId;

            DB::table('structures')->insert([
                'id' => $structureId,
                'nom' => $struct['nom'],
                'sigle' => $struct['sigle'],
                'type_structure' => $faker->randomElement($typesStructures),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Utilisateurs (modification de la table users existante)
        $users = [];
        $fonctions = ['Directeur', 'Chef de Service', 'Responsable Programme', 'Coordinateur', 'Chargé de Mission'];

        for ($i = 0; $i < 5; $i++) {
            $userId = (string) Str::ulid();
            $users[] = $userId;

            DB::table('users')->insert([
                'id' => $userId,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'nom' => $faker->lastName,
                'prenom' => $faker->firstName,
                'telephone' => $faker->unique()->phoneNumber,
                'matricule' => 'MAT' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'fonction' => $faker->randomElement($fonctions),
                'structure_id' => $faker->randomElement($structures),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Programmes d'activités
        $programmesActivites = [];
        $annees = ['2023', '2024', '2025'];
        $libellesProgrammes = [
            'Programme National de Santé Communautaire',
            'Programme d\'Amélioration de l\'Education de Base',
            'Programme de Développement des Infrastructures Rurales',
            'Programme de Renforcement des Capacités Institutionnelles',
            'Programme de Lutte contre la Pauvreté'
        ];

        foreach ($libellesProgrammes as $index => $libelle) {
            $programmeActiviteId = (string) Str::ulid();
            $programmesActivites[] = $programmeActiviteId;

            DB::table('programmes_activites')->insert([
                'id' => $programmeActiviteId,
                'libelle' => $libelle,
                'annee' => $faker->randomElement($annees),
                'file' => $faker->optional()->word . '.pdf',
                'date_generation' => $faker->dateTimeBetween('-1 year', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Programmes budgétaires
        $programmesBudgetaires = [];
        for ($i = 0; $i < 5; $i++) {
            $programmeBudgetaireId = (string) Str::ulid();
            $programmesBudgetaires[] = $programmeBudgetaireId;

            DB::table('programmes_budgetaires')->insert([
                'id' => $programmeBudgetaireId,
                'libelle' => $faker->sentence(6),
                'numero_programme' => 'P' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'programme_activite_id' => $faker->randomElement($programmesActivites),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Objectifs stratégiques
        $objectifsStrategiques = [];
        for ($i = 0; $i < 30; $i++) {
            $objectifStrategiqueId = (string) Str::ulid();
            $objectifsStrategiques[] = $objectifStrategiqueId;

            DB::table('objectifs_strategiques')->insert([
                'id' => $objectifStrategiqueId,
                'libelle' => 'Objectif stratégique : ' . $faker->sentence(8),
                'programme_budgetaire_id' => $faker->randomElement($programmesBudgetaires),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Actions
        $actions = [];
        for ($i = 0; $i < 60; $i++) {
            $actionId = (string) Str::ulid();
            $actions[] = $actionId;

            DB::table('actions')->insert([
                'id' => $actionId,
                'libelle' => 'Action : ' . $faker->sentence(6),
                'objectif_strategique_id' => $faker->randomElement($objectifsStrategiques),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Objectifs spécifiques
        $objectifsSpecifiques = [];
        for ($i = 0; $i < 100; $i++) {
            $objectifSpecifiqueId = (string) Str::ulid();
            $objectifsSpecifiques[] = $objectifSpecifiqueId;

            DB::table('objectifs_specifiques')->insert([
                'id' => $objectifSpecifiqueId,
                'libelle' => 'Objectif spécifique : ' . $faker->sentence(7),
                'action_id' => $faker->randomElement($actions),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Activités
        $activites = [];
        $periodes = DB::table('periodes')->pluck('id')->toArray(); // récupérer les id des périodes existantes

        for ($i = 0; $i < 150; $i++) {
            $activiteId = (string) Str::ulid();
            $activites[] = $activiteId;

            // Insertion dans la table activites
            DB::table('activites')->insert([
                'id' => $activiteId,
                'libelle' => 'Activité : ' . $faker->sentence(5),
                'cout_previsionnel' => $faker->randomFloat(2, 10000, 1000000),
                'objectif_specifique_id' => $faker->randomElement($objectifsSpecifiques),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attacher entre 1 et 4 périodes aléatoires à cette activité
            $periodesChoisies = $faker->randomElements($periodes, rand(1, count($periodes)));

            foreach ($periodesChoisies as $periodeId) {
                DB::table('activite_periode')->insert([
                    'activite_id' => $activiteId,
                    'periode_id' => $periodeId,
                ]);
            }
        }

        // 9. Indicateurs programmes
        $indicateursProgrammes = [];
        $unitesMesure = ['%', 'Nombre', 'Km', 'Unité', 'Pourcentage', 'Ratio', 'Index'];

        for ($i = 0; $i < 200; $i++) {
            $indicateurId = (string) Str::ulid();
            $indicateursProgrammes[] = $indicateurId;

            // Déterminer aléatoirement quel type d'entité lier
            $linkType = $faker->randomElement(['objectif_specifique', 'objectif_strategique', 'activite']);
            $objectifSpecifiqueId = null;
            $objectifStrategiqueId = null;
            $activiteId = null;

            switch ($linkType) {
                case 'objectif_specifique':
                    $objectifSpecifiqueId = $faker->randomElement($objectifsSpecifiques);
                    break;
                case 'objectif_strategique':
                    $objectifStrategiqueId = $faker->randomElement($objectifsStrategiques);
                    break;
                case 'activite':
                    $activiteId = $faker->randomElement($activites);
                    break;
            }

            DB::table('indicateurs_programmes')->insert([
                'id' => $indicateurId,
                'libelle' => 'Indicateur : ' . $faker->sentence(4),
                'unite_mesure' => $faker->randomElement($unitesMesure),
                'valeur_ref' => (string) $faker->numberBetween(0, 100),
                'valeur_cible' => (string) $faker->numberBetween(80, 150),
                'objectif_specifique_id' => $objectifSpecifiqueId,
                'objectif_strategique_id' => $objectifStrategiqueId,
                'activites_id' => $activiteId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 10. Suivis
        for ($i = 0; $i < 300; $i++) {
            DB::table('suivis')->insert([
                'id' => (string) Str::ulid(),
                'cout_effectif' => $faker->optional(0.8)->randomFloat(2, 5000, 800000),
                'resultat_atteint' => $faker->optional(0.9)->randomFloat(2, 0, 100),
                'observations' => $faker->optional(0.7)->sentence(10),
                'periode' => $faker->randomElement($periodes),
                'indicateur_programme_id' => $faker->randomElement($indicateursProgrammes),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 11. Rapports d'activités
        $periodesRapport = ['Mensuel', 'Trimestriel', 'Semestriel', 'Annuel'];

        for ($i = 0; $i < 25; $i++) {
            DB::table('rapports_activites')->insert([
                'id' => (string) Str::ulid(),
                'libelle' => 'Rapport ' . $faker->randomElement($periodesRapport) . ' - ' . $faker->sentence(3),
                'periode_rapport' => $faker->randomElement($periodesRapport),
                'date_rapport' => $faker->dateTimeBetween('-6 months', 'now'),
                'file' => $faker->optional(0.8)->word . '_rapport.pdf',
                'programme_activite_id' => $faker->randomElement($programmesActivites),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 12. Table pivot programme_budgetaire_structure
        $pivotPairs = [];
        for ($i = 0; $i < 40; $i++) {
            $structureId = $faker->randomElement($structures);
            $programmeBudgetaireId = $faker->randomElement($programmesBudgetaires);
            $key = $structureId . '|' . $programmeBudgetaireId;

            if (!in_array($key, $pivotPairs)) {
                $pivotPairs[] = $key;
                DB::table('programme_budgetaire_structure')->insert([
                    'structure_id' => $structureId,
                    'programme_budgetaire_id' => $programmeBudgetaireId,
                ]);
            }
        }

        // 13. Responsables
        $responsablePairs = [];
        for ($i = 0; $i < 30; $i++) {
            $userId = $faker->randomElement($users);
            $programmeBudgetaireId = $faker->randomElement($programmesBudgetaires);
            $key = $userId . '|' . $programmeBudgetaireId;

            if (!in_array($key, $responsablePairs)) {
                $responsablePairs[] = $key;
                $dateDebut = $faker->dateTimeBetween('-2 years', '-6 months');

                DB::table('responsables')->insert([
                    'user_id' => $userId,
                    'programme_budgetaire_id' => $programmeBudgetaireId,
                    'date_debut' => $dateDebut,
                    'date_fin' => $faker->optional(0.3)->dateTimeBetween($dateDebut, 'now'),
                ]);
            }
        }

        $this->command->info('Base de données seedée avec succès !');
        $this->command->info('Structures créées: ' . count($structures));
        $this->command->info('Utilisateurs créés: ' . count($users));
        $this->command->info('Programmes d\'activités créés: ' . count($programmesActivites));
        $this->command->info('Programmes budgétaires créés: ' . count($programmesBudgetaires));
        $this->command->info('Objectifs stratégiques créés: ' . count($objectifsStrategiques));
        $this->command->info('Actions créées: ' . count($actions));
        $this->command->info('Objectifs spécifiques créés: ' . count($objectifsSpecifiques));
        $this->command->info('Activités créées: ' . count($activites));
        $this->command->info('Indicateurs programmes créés: ' . count($indicateursProgrammes));
    }
}