<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ CATEGORIES
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle', 250);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2️⃣ TABLEAUX
        Schema::create('tableaux', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle', 250);
            $table->string('description', 250)->nullable();
            $table->foreignUlid('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3️⃣ REGLES DE GESTION
        Schema::create('regles_de_gestion', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('libelle', 250);
            $table->string('operations', 250);
            $table->foreignUlid('tableau_id')->references('id')->on('tableaux')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4️⃣ ASSIGNATIONS
        Schema::create('assignations', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('tableau_id')->references('id')->on('tableaux')->cascadeOnDelete();
            $table->foreignUlid('structure_id')->constrained('structures')->cascadeOnDelete();
            $table->string('version', 250)->nullable();
            $table->string('source', 250)->nullable();
            $table->integer('statut')->default(1);
            $table->string('responsable', 250)->nullable();
            $table->date('date_debut')->nullable();
            $table->string('date_fin', 250)->nullable();
            $table->year('annee')->nullable();
            $table->string('periode', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5️⃣ LIGNES TABLEAU (avec sous-lignes)
        Schema::create('lignes_tableau', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('nom_ligne', 250);
            $table->string('ordre', 250);

            $table->foreignUlid('tableau_id')->references('id')->on('tableaux')->cascadeOnDelete();

            // auto-référence pour sous-lignes



            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('lignes_tableau', function (Blueprint $table) {
            $table->foreignUlid('parent_id')->nullable()->constrained('lignes_tableau')->restrictOnDelete()->cascadeOnUpdate();
        });



        // 6️⃣ COLONNES TABLEAU (avec sous-colonnes)
        Schema::create('colonnes_tableau', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('colonne_ligne', 250);
            $table->string('ordre', 250);

            $table->foreignUlid('tableau_id')->references('id')->on('tableaux')->cascadeOnDelete();

            // auto-référence pour sous-colonnes


            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table(('colonnes_tableau'), function (Blueprint $table) {
            $table->foreignUlid('parent_id')->nullable()->constrained('colonnes_tableau')->restrictOnDelete()->cascadeOnUpdate();
        });

        // 7️⃣ CELLULES
        Schema::create('cellules', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();

            $table->foreignUlid('ligne_id')->references('id')->on('lignes_tableau')->cascadeOnDelete();


            $table->foreignUlid('colonne_id')->references('id')->on('colonnes_tableau')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // 8️⃣ SOUMISSIONS TABLEAU
        Schema::create('soumissions_tableau', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('valeur', 50);


            $table->foreignUlid('tableau_id')->references('id')->on('tableaux')->cascadeOnDelete();

            $table->foreignUlid('structure_id')->constrained('structures')->cascadeOnDelete();

            $table->foreignUlid('assignation_id')->constrained('assignations')->cascadeOnDelete();

            $table->foreignUlid('cellule_id')->constrained('cellules')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soumissions_tableau');
        Schema::dropIfExists('cellules');
        Schema::dropIfExists('colonnes_tableau');
        Schema::dropIfExists('lignes_tableau');
        Schema::dropIfExists('assignations');
        Schema::dropIfExists('regles_de_gestion');
        Schema::dropIfExists('tableaux');
        Schema::dropIfExists('categories');
    }
};
