<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 30; $i++) {
            $data = $this->generateRandomData();
            Evento::create($data);
        }
    }

    private function generateRandomData()
    {
        $faker = \Faker\Factory::create();

        $codigos_pais = ['ES', 'MX', 'AR', 'CO', 'PE', 'CL', 'VE', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'UY', 'PA'];
        $categorias = ['Cursos', 'Convivencias', 'Encuentros'];

        return [
            'titulo' => $faker->sentence(5),
            'slug' => $faker->slug(),
            'categoria' => $faker->randomElement($categorias),
            'descripcion' => $faker->paragraph(1),
            'texto' => $faker->paragraph(4),
            'imagen' => $faker->imageUrl(),
            'fecha_inicio' => $faker->date(),
            'fecha_fin' => $faker->date(),

            /* 'provincia' => $faker->state(),
            'direccion' => $faker->address(),
            'codigo' => $faker->postcode(),
            'telefono' => $faker->phoneNumber(),
            'social' => $faker->randomElement(['Facebook', 'Twitter', 'Instagram']),
            'email' => $faker->email()
            */
        ];
    }
}
