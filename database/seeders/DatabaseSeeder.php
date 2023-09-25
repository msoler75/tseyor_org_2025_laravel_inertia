<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Informe;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <=200; $i++) {
            $data = $this->generateRandomData();
            Informe::create($data);
        }
    }

    private function generateRandomData()
    {
        $faker = \Faker\Factory::create();

        //$codigos_pais = ['ES', 'MX', 'AR', 'CO', 'PE', 'CL', 'VE', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'UY', 'PA'];
        // $categorias = ['Meditaciones', 'Cuentos', 'Clásica', 'Canciones', 'Reflexiones', 'Talleres'];
        $categorias = //['Bases', 'Planetas', 'Otros lugares'];
        ['Actas','Orden del día', 'Informe', 'Resumen', 'Otros'];
        /*
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
    ];*/

    $equipos = [15, 2, 3, 4];

        return [
            'titulo' => $faker->text(60),
            //'name' => $faker->userName(),
            // 'slug' => $faker->slug(),
            //'email' =>$faker->email(),
            //'password'=>$faker->password(),
            'categoria' => $faker->randomElement($categorias),
            //'audio' => $faker->randomElement($files),
            'equipo_id' => $faker->randomElement($equipos),
            'descripcion' => $faker->paragraph(1),
            'texto' => $faker->paragraph(5),
            'visibilidad' => 'P'
            //'imagen' => $faker->getImageUrl(800, 600)
            //'profile_photo_path' =>$faker->getImageUrl(600, 600),
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
