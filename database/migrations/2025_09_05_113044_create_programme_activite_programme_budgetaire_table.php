<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('programme_activite_programme_budgetaire', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('programme_activite_id')->constrained('programmes_activites')->onDelete('cascade');
            $table->foreignUlid('programme_budgetaire_id')->constrained('programmes_budgetaires')->onDelete('cascade');
            $table->unique(['programme_activite_id', 'programme_budgetaire_id'], 'unique_programme_relation');
        });
    }

    public function down()
    {
        Schema::dropIfExists('programme_activite_programme_budgetaire');
    }
};
