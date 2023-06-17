<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entrada;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 300; $i++) {
            $data = $this->generateRandomData();
            Entrada::create($data);
        }
    }

    private function generateRandomData()
    {
        $faker = \Faker\Factory::create();

        return [
            'titulo' => $faker->sentence(5),
            'slug' => $faker->slug(),
            'descripcion' => $faker->paragraph(3),
            'texto' => $faker->paragraph(12),
            'categoria' => $faker->word(),
            'imagen' => $faker->imageUrl(),
            'estado' => 'P',
            'published_at' => $faker->dateTimeBetween('-3 year', 'now'),
        ];
    }
}
