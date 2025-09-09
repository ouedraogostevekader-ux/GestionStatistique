<?php
namespace Database\Factories;

use App\Models\Tableau;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TableauFactory extends Factory
{
    protected $model = Tableau::class;

    public function definition()
    {
        return [
            'id' => Str::ulid(),
            'libelle' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'categorie_id' => Categorie::factory(),
        ];
    }
}