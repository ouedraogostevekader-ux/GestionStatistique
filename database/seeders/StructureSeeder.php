<?php

namespace Database\Seeders;

use App\Models\Structure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $structure = Structure::create(['nom' => 'DGESS', 'sigle' => 'DGESS', 'type_structure' => 'Direction generale']);
    }
}
