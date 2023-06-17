<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Libro;

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
            $bookData = $this->generateRandomBookData();
            Libro::create($bookData);
        }
    }

    private function generateRandomBookData()
    {
        $faker = \Faker\Factory::create();

        return [
            'titulo' => $faker->sentence(5),
            'slug' => $faker->slug(),
            'descripcion' => $faker->paragraph(3),
            'categoria' => $faker->word(),
            'imagen' => $faker->imageUrl(),
            'estado' => 'P',
            'edicion' => $faker->numberBetween(1, 10),
            'paginas' => $faker->numberBetween(50, 1000),
            'pdf' => $faker->url(),
            'published_at' => $faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
