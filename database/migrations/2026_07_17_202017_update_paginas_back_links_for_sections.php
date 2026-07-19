<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $backLinks = [
        // Section: Curso
        'inscripcion'            => ['ruta' => 'cursos',         'texto' => 'Curso'],
        'mis-primeros-pasos'     => ['ruta' => 'cursos',         'texto' => 'Curso'],
        'preguntas'              => ['ruta' => 'cursos',         'texto' => 'Curso'],
        'terminos'               => ['ruta' => 'cursos',         'texto' => 'Curso'],

        // Section: Biblioteca
        'comunicados'            => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'audios'                 => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'libros'                 => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'meditaciones'           => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'radio'                  => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'psicografias'           => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'videos'                 => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],
        'galerias'               => ['ruta' => 'biblioteca',     'texto' => 'Biblioteca'],

        // Section: Filosofía
        'guias'                  => ['ruta' => 'filosofia',      'texto' => 'Filosofía'],
        'origenes-de-tseyor'     => ['ruta' => 'filosofia',      'texto' => 'Filosofía'],

        // Section: Quiénes somos
        'ong'                    => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],
        'asociacion'             => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],
        'utg'                    => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],
        'contactos'              => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],
        'centros'                => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],
        'contactar'              => ['ruta' => 'quienes-somos',  'texto' => 'Quiénes somos'],

        // Section: Novedades
        'eventos'                => ['ruta' => 'novedades',      'texto' => 'Novedades'],
        'noticias'               => ['ruta' => 'novedades',      'texto' => 'Novedades'],
        'boletines'              => ['ruta' => 'novedades',      'texto' => 'Novedades'],

        // Section: Miembros
        'equipos'                => ['ruta' => 'miembros',       'texto' => 'Miembros'],
        'usuarios'               => ['ruta' => 'miembros',       'texto' => 'Miembros'],
    ];

    private array $filosofiaTematicas = [
        'confederacion-de-mundos-habitados-de-la-galaxia',
        'el-rayo-sincronizador',
        'salto-cuantico',
        'especializacion',
        'la-autoobservacion',
        'retroalimentacion',
        'espejos',
        'las-sociedades-armonicas',
    ];

    public function up(): void
    {
        // 1. Update pages mapped by route
        foreach ($this->backLinks as $ruta => $link) {
            DB::table('paginas')
                ->where('ruta', $ruta)
                ->update([
                    'atras_ruta' => $link['ruta'],
                    'atras_texto' => $link['texto'],
                ]);
        }

        // 2. Update filosofia thematic pages → filosofia/temas
        DB::table('paginas')
            ->whereIn('ruta', $this->filosofiaTematicas)
            ->update([
                'atras_ruta' => 'filosofia/temas',
                'atras_texto' => 'Todos los temas',
            ]);
    }

    public function down(): void
    {
        // 1. Clear back links for all mapped section pages
        $allRutas = array_merge(
            array_keys($this->backLinks),
            $this->filosofiaTematicas
        );

        DB::table('paginas')
            ->whereIn('ruta', $allRutas)
            ->update([
                'atras_ruta' => null,
                'atras_texto' => null,
            ]);
    }
};
