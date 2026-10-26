<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignations', function (Blueprint $table) {
            $table->text('motif_rejet')->nullable()->after('statut');
            $table->timestamp('date_validation')->nullable()->after('motif_rejet');
            $table->foreignUlid('valide_par')->nullable()->after('date_validation')->constrained('users')->nullOnDelete();
            $table->timestamp('date_rejet')->nullable()->after('valide_par');
            $table->foreignUlid('rejete_par')->nullable()->after('date_rejet')->constrained('users')->nullOnDelete();
            $table->timestamp('derniere_relance_at')->nullable()->after('rejete_par');
            $table->integer('relances_count')->default(0)->after('derniere_relance_at');
        });

        Schema::table('versions', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('annee');
            $table->timestamp('date_verrouillage')->nullable()->after('is_locked');
            $table->foreignUlid('locked_by')->nullable()->after('date_verrouillage')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('versions', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['is_locked', 'date_verrouillage', 'locked_by']);
        });

        Schema::table('assignations', function (Blueprint $table) {
            $table->dropForeign(['valide_par']);
            $table->dropForeign(['rejete_par']);
            $table->dropColumn([
                'motif_rejet',
                'date_validation',
                'valide_par',
                'date_rejet',
                'rejete_par',
                'derniere_relance_at',
                'relances_count'
            ]);
        });
    }
};
