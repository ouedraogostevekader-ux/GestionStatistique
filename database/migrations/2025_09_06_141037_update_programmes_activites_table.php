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
        Schema::table('programmes_activites', function (Blueprint $table) {
            $table->renameColumn('file', 'file_pdf');

            // ✅ Ajouter de nouveaux champs
            $table->string('file_excel')->nullable()->after('file_pdf');
        });

        // Table pivot
/*         Schema::create('programme_activite_programme_budgetaire', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('programme_activite_id')->constrained('programmes_activites')->cascadeOnDelete();
            $table->foreignUlid('programme_budgetaire_id')->constrained('programmes_budgetaires')->cascadeOnDelete();
            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programmes_activites', function (Blueprint $table) {
            $table->dropColumn('file_pdf');
            $table->dropColumn('file_excel');
        });
    }
};
