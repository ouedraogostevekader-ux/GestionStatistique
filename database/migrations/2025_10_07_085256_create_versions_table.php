<?php

use App\Models\Version;
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
        Schema::create('versions', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('code');
            $table->string('libelle')->nullable();
            $table->string('annee')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Version::create([
            'code' => 'V1',
            'libelle' => 'Premiere version',
            'annee' => '2025'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
