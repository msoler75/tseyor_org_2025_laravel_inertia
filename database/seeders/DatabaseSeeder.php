<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Audio;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 120; $i++) {
            $data = $this->generateRandomData();
            Audio::create($data);
        }
    }

    private function generateRandomData()
    {
        $faker = \Faker\Factory::create();

        $codigos_pais = ['ES', 'MX', 'AR', 'CO', 'PE', 'CL', 'VE', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'UY', 'PA'];
        $categorias = ['Meditaciones', 'Cuentos', 'Clásica', 'Canciones', 'Reflexiones', 'Talleres'];
        $files = ['https://cdn.pixabay.com/audio/2023/04/03/audio_047543feac.mp3',
        'https://cdn.pixabay.com/audio/2023/01/01/audio_816821e627.mp3',
        'https://cdn.pixabay.com/audio/2022/08/25/audio_4f3b0a816e.mp3',
        'https://cdn.pixabay.com/audio/2022/08/17/audio_eb3864cceb.mp3',
        'https://cdn.pixabay.com/audio/2022/07/06/audio_bafec9fdb3.mp3',
        'https://cdn.pixabay.com/audio/2022/09/02/audio_72502a492a.mp3',
        'https://cdn.pixabay.com/audio/2022/01/18/audio_d3a7b18ab3.mp3',
        'https://cdn.pixabay.com/audio/2022/05/31/audio_cc96befbf4.mp3',
        'https://cdn.pixabay.com/audio/2022/08/29/audio_38be17baf6.mp3',
        'https://cdn.pixabay.com/audio/2022/03/24/audio_3ec5eb6ba2.mp3',
        'https://cdn.pixabay.com/audio/2023/03/02/audio_88af125093.mp3',
        'https://cdn.pixabay.com/audio/2021/12/01/audio_2fe16fd258.mp3',
        'https://cdn.pixabay.com/audio/2022/03/10/audio_f485a07711.mp3'
    ];

        return [
            'titulo' => $faker->sentence(5),
            'slug' => $faker->slug(),
            'categoria' => $faker->randomElement($categorias),
            'audio' => $faker->randomElement($files),
            'descripcion' => $faker->paragraph(1),
            'visibilidad'=>'P'

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
