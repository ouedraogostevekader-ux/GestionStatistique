<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Structures
        Schema::create('structures', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('nom')->unique();
            $table->string('sigle')->unique();
            $table->string('type_structure');
            $table->softDeletes();
            $table->timestamps();
        });


        // Programmes d’activités
        Schema::create('programmes_activites', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->year('annee')->default(date('Y'));
            $table->string('file')->nullable();
            $table->dateTime('date_generation');
            $table->softDeletes();
            $table->timestamps();
        });

        // Programmes budgétaires
        Schema::create('programmes_budgetaires', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->string('numero_programme');
            $table->year('annee')->default(date('Y'));
            $table->boolean('isActive')->default(true);
            $table->foreignUlid('programme_activite_id')->nullable()->constrained('programmes_activites')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // Objectifs stratégiques
        Schema::create('objectifs_strategiques', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->year('annee')->default(date('Y'));
            $table->foreignUlid('programme_budgetaire_id')->constrained('programmes_budgetaires')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // Rapports d’activités
        Schema::create('rapports_activites', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->string('periode_rapport');
            $table->dateTime('date_rapport');
            $table->string('file')->nullable();
            $table->foreignUlid('programme_activite_id')->constrained('programmes_activites')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->unique()->nullable();
            $table->string('matricule')->unique();
            $table->string('fonction')->nullable();
            $table->foreignUlid('structure_id')->constrained('structures')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
        });

        // Actions
        Schema::create('actions', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->year('annee')->default(date('Y'));
            $table->foreignUlid('objectif_strategique_id')->constrained('objectifs_strategiques')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // Objectifs spécifiques
        Schema::create('objectifs_specifiques', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->year('annee')->default(date('Y'));
            $table->foreignUlid('action_id')->constrained('actions')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // Activités
        Schema::create('activites', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->decimal('cout_previsionnel', 15, 2)->nullable();
            $table->string('libelle', 500);
            $table->year('annee')->default(date('Y'));
            $table->foreignUlid('objectif_specifique_id')->constrained('objectifs_specifiques')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('periodes', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle')->unique(); // Ex: "T1", "T2", "T3", "T4"
            $table->softDeletes();
            $table->timestamps();
        });

        // Indicateurs programmes
        Schema::create('indicateurs_programmes', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle');
            $table->string('unite_mesure');
            $table->string('valeur_ref');
            $table->string('valeur_cible');
            $table->year('annee')->default(date('Y'));
            $table->foreignUlid('objectif_specifique_id')->nullable()->constrained('objectifs_specifiques')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('objectif_strategique_id')->nullable()->constrained('objectifs_strategiques')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('activites_id')->nullable()->constrained('activites')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });

        // Suivi
        Schema::create('suivis', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->decimal('cout_effectif', 15, 2)->nullable();
            $table->float('resultat_atteint')->nullable();
            $table->string('observations')->nullable();
            $table->string('periode');
            $table->foreignUlid('indicateur_programme_id')->constrained('indicateurs_programmes')->restrictOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });



        //table pivot
        Schema::create('programme_budgetaire_structure', function (Blueprint $table) {
            $table->foreignUlid('structure_id')->constrained('structures')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('programme_budgetaire_id')->constrained('programmes_budgetaires')->restrictOnDelete()->cascadeOnUpdate();
            $table->primary(['structure_id', 'programme_budgetaire_id']);
        });
        Schema::create('activite_periode', function (Blueprint $table) {
            $table->foreignUlid('activite_id')->constrained('activites')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('periode_id')->constrained('periodes')->restrictOnDelete()->cascadeOnUpdate();
            $table->year('annee')->default(date('Y'));
            $table->primary(['activite_id', 'periode_id']); // clé composite
        });

        Schema::create('responsables', function (Blueprint $table) {
            $table->foreignUlid('user_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('programme_budgetaire_id')->constrained('programmes_budgetaires')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->primary(['user_id', 'programme_budgetaire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsables');
        Schema::dropIfExists('programme_budgetaire_structure');

        Schema::dropIfExists('activites');
        Schema::dropIfExists('suivis');
        Schema::dropIfExists('indicateurs_programmes');
        Schema::dropIfExists('objectifs_specifiques');
        Schema::dropIfExists('actions');

        Schema::dropIfExists('rapports_activites');
        Schema::dropIfExists('objectifs_strategiques');
        Schema::dropIfExists('programmes_budgetaires');
        Schema::dropIfExists('programmes_activites');


        Schema::dropIfExists('structures');
    }
};
