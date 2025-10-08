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
            $table->string('periode')->nullable()->change();
            $table->string('date_debut')->nullable()->change();
            $table->string('date_fin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignations', function (Blueprint $table) {
            $table->string('periode')->nullable(false)->change();
            $table->string('date_debut')->nullable(false)->change();
            $table->string('date_fin')->nullable(false)->change();
        });
    }
};
