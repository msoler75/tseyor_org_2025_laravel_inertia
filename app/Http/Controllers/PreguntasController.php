<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Preguntas;
use App\Models\Libro;
use App\Pigmalion\SEO;

class PreguntasController extends Controller
{
    private array $ejemplosPorSlug = [
        'extraterrestres' => [
            ['text' => '¿Existen materialmente o son seres de otra dimensión o plano?', 'anchor' => '_Toc301129892'],
            ['text' => '¿Por qué no se presentan y arreglan la situación mundial?', 'anchor' => '_Toc301129938'],
            ['text' => '¿Cómo puede alguien tomar contacto con los HM?', 'anchor' => '_Toc301129944'],
            ['text' => '¿Cómo es la tecnología de los HM? ¿Cómo son sus naves?', 'anchor' => '_Toc301129896'],
        ],
        'salto-cuantico' => [
            ['text' => '¿Estamos ante un cambio de eras? ¿Llega una Nueva Era?', 'anchor' => '_Toc301130026'],
            ['text' => '¿Hacia dónde se dirige la humanidad?', 'anchor' => '_Toc301130028'],
            ['text' => '¿Cómo nos afecta este proceso de cambio ahora mismo?', 'anchor' => '_Toc301130049'],
            ['text' => '¿Cómo podemos afrontar o comprender estos desequilibrios emocionales?', 'anchor' => '_Toc301130054'],
        ],
        'grupo-tseyor' => [
            ['text' => '¿Qué es el grupo Tseyor?', 'anchor' => '_Toc301130188'],
            ['text' => '¿Es esto una secta?', 'anchor' => '_Toc301130199'],
            ['text' => '¿Qué significa la palabra Tseyor?', 'anchor' => '_Toc301130192'],
            ['text' => '¿Cuál es el mensaje central de Tseyor?', 'anchor' => '_Toc301130196'],
            ['text' => '¿En Tseyor hay líderes? ¿Hay algún tipo de jerarquía?', 'anchor' => '_Toc301130201'],
        ],
    ];

    public function index()
    {
        $secciones = Preguntas::select('titulo', 'id', 'slug', 'descripcion', 'texto')->get();

        foreach ($secciones as $seccion) {
            $seccion->ejemplos = $this->ejemplosPorSlug[$seccion->slug] ?? [];
        }

        $libro = Libro::where('slug', 'preguntas-y-respuestas-tseyor')->first();

        return Inertia::render('Preguntas/Index', [
            'secciones' => $secciones,
            'libro' => $libro
        ])
            ->withViewData(SEO::get('preguntas-frecuentes'));
    }

    public function show($id)
    {

        if (is_numeric($id)) {
            $preguntas = Preguntas::findOrFail($id);
        } else {
            $preguntas = Preguntas::where('slug', $id)->firstOrFail();
        }

        $file = $preguntas->texto;

        // obtener la ruta ./resources/preguntas/{$f}
        $contenido_html = file_get_contents(base_path('resources/preguntas/' . $file));

        return Inertia::render('Preguntas/PreguntasSeccion', [
            'titulo' => $preguntas->titulo,
            'texto' => $contenido_html
        ])
            ->withViewData(SEO::from($preguntas));
    }
}
