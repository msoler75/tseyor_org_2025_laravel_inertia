<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contacto;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 60; $i++) {
            $data = $this->generateRandomData();
            Contacto::create($data);
        }
    }

    private function generateRandomData()
    {
        $faker = \Faker\Factory::create();

        $codigos_pais = ['ES', 'MX', 'AR', 'CO', 'PE', 'CL', 'VE', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'UY', 'PA'];

        return [
            'nombre' => $faker->sentence(5),
            'slug' => $faker->slug(),
            'pais' => $faker->randomElement($codigos_pais),
            'poblacion' => $faker->sentence(2),
            'imagen' => $faker->imageUrl(),


            'provincia' => $faker->state(),
            'direccion' => $faker->address(),
            'codigo' => $faker->postcode(),
            'telefono' => $faker->phoneNumber(),
            'social' => $faker->randomElement(['Facebook', 'Twitter', 'Instagram']),
            'email' => $faker->email()
        ];
    }
}
