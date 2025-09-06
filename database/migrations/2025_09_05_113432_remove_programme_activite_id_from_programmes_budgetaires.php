<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('programmes_budgetaires', function (Blueprint $table) {
            $table->dropForeign(['programme_activite_id']);
            $table->dropColumn('programme_activite_id');
        });
    }

    public function down()
    {
        Schema::table('programmes_budgetaires', function (Blueprint $table) {
            $table->unsignedBigInteger('programme_activite_id')->nullable();
            $table->foreign('programme_activite_id')->references('id')->on('programmes_activites');
        });
    }
};
