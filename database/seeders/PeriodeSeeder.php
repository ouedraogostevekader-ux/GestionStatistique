<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periodes = ['T1', 'T2', 'T3', 'T4'];

        foreach ($periodes as $periode) {
            DB::table('periodes')->insert([
                'id' => (string) Str::ulid(),
                'libelle' => $periode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
