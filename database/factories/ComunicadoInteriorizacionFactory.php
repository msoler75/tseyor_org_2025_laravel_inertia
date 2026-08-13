<?php

namespace Database\Factories;

use App\Models\ComunicadoInteriorizacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ComunicadoInteriorizacionFactory extends Factory
{
    protected $model = ComunicadoInteriorizacion::class;

    /**
     * Ciclos de entrenamiento de interiorización disponibles.
     */
    protected array $ciclos = [
        'ciclo-primavera-2024',
        'ciclo-otono-2024',
        'ciclo-primavera-2025',
        'ciclo-otono-2025',
        'Ciclo 1',
        'Ciclo 2',
    ];

    /**
     * Temas típicos de las sesiones de interiorización.
     */
    protected array $temas = [
        'La meditación contemplativa',
        'El despertar de la conciencia',
        'La respiración consciente',
        'El viaje interior',
        'La conexión con el aquí y ahora',
        'El silencio como herramienta',
        'La atención plena',
        'El desapego mental',
        'La energía del corazón',
        'Los niveles de conciencia',
        'La visualización creativa',
        'El equilibrio emocional',
    ];

    public function definition(): array
    {
        $titulo = $this->faker->randomElement($this->temas).' '.$this->faker->numberBetween(1, 60);
        $fecha = $this->faker->dateTimeBetween('-3 years', 'now');

        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.$this->faker->unique()->numberBetween(100, 999),
            'descripcion' => $this->faker->sentence(10),
            'texto' => $this->generarTexto(),
            'nivel' => $this->faker->randomElement([1, 1, 2]),
            'ciclo' => $this->faker->randomElement($this->ciclos),
            'numero' => $this->faker->optional(0.6)->numberBetween(1, 25),
            'fecha_comunicado' => $fecha->format('Y-m-d'),
            'ano' => (int) $fecha->format('Y'),
            'imagen' => null,
            'audios' => null,
            'visibilidad' => $this->faker->randomElement(['P', 'P', 'P', 'B']),
        ];
    }

    private function generarTexto(): string
    {
        $parrafos = [];

        for ($i = 0; $i < rand(4, 8); $i++) {
            $parrafos[] = $this->faker->paragraphs(2)[0];
        }

        return implode("\n\n", $parrafos);
    }
}