<?php

namespace Database\Factories;

use App\Models\Entrada;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EntradaFactory extends Factory
{
    protected $model = Entrada::class;

    public function definition()
    {
        $titulo = $this->faker->sentence(4);
        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo) . '-' . $this->faker->unique()->randomNumber(5),
            'descripcion' => $this->faker->paragraph(2),
            'categoria' => $this->faker->randomElement(['Pueblo Tseyor', 'ONG', 'Otros']),
            'texto' => $this->faker->paragraph(5),
            'imagen' => null,
            'published_at' => now(),
            'visibilidad' => $this->faker->randomElement(['P', 'B']),
        ];
    }
}
