<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('activites', 'created_by')) {
                $table->foreignUlid('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            }

            if (!Schema::hasColumn('activites', 'updated_by')) {
                $table->foreignUlid('updated_by')
                    ->nullable()
                    ->constrained('users')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            // Supprimer les contraintes de clé étrangère d'abord
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Puis supprimer les colonnes
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};