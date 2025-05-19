<?php

namespace App\Http\Controllers;

use App\Models\Boletin;
use Inertia\Inertia;
use App\Pigmalion\SEO;
use Illuminate\Http\Request;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Entrada;
use App\Models\Noticia;
use App\Pigmalion\Markdown;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Notifications\BoletinGenerado;


class BoletinesController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los boletines recientes según la búsqueda
        $resultados = Boletin::select(['id', 'titulo', 'texto as descripcion', 'updated_at', 'tipo'])
            ->when($buscar, function ($query, $buscar) {
                $query->where('titulo', 'LIKE', "%$buscar%")
                    ->orWhere('texto', 'LIKE', "%$buscar%");
            })
            ->when($categoria, function ($query, $categoria) {
                $query->where('tipo', $categoria);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria]);


        $categorias = (new Boletin())->getCategorias();

        return Inertia::render('Boletines/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])->withViewData(SEO::get('boletines'));
    }

    public function ver($id)
    {
        $boletin = Boletin::findOrFail($id);
        return Inertia::render('Boletines/Boletin', ['boletin' => $boletin]);
    }




    public function generarBoletin(Request $request)
    {
        $tipo = $request->input('tipo');
        $hoy = \Carbon\Carbon::now();
        $semana = $hoy->weekOfYear;
        $mes = $hoy->month;
        $anyo = $hoy->year;

        // Determinar rango de fechas y título según el tipo
        if ($tipo == 'semanal') {
            $semana = $semana - 1;
            if ($semana < 1) {
                $semana = 52;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfWeek()->subWeek();
            $fin = now()->startOfWeek()->subWeek()->endOfWeek();
            $titulo = "Boletín Tseyor semanal 2025 - semana $semana";
        } elseif ($tipo == 'bisemanal') {
            $semana = $semana - 2;
            if ($semana < 1) {
                $semana += 52;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfWeek()->subWeeks(2);
            $fin = now()->startOfWeek()->subWeek()->endOfWeek();
            $titulo = "Boletín Tseyor 2025 - semanas $semana y " . ($semana + 1);
        } elseif ($tipo == 'mensual') {
            $mes = $mes - 1;
            if ($mes < 1) {
                $mes += 12;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfMonth()->subMonth();
            $fin = now()->startOfMonth()->subMonth()->endOfMonth();
            $titulo = "Boletín Tseyor de " . $inicio->translatedFormat('F Y');
        } else {
            $mes = $mes - 2;
            if ($mes % 2 == 0) {
                $mes--;
            }
            if ($mes < 1) {
                $mes += 12;
                $anyo = $anyo - 1;
            }
            $inicio = now()->startOfMonth()->subMonths(2);
            $fin = $inicio->copy()->addMonth()->endOfMonth();
            $titulo = "Boletín Tseyor de " . $inicio->translatedFormat('F Y') . ' y ' .
                $fin->translatedFormat('F Y');
        }

        // Parametrización de tipos de contenido
        $tiposContenido = [
            'noticias' => [
                'model' => Noticia::class,
                'date_field' => 'published_at',
                'section_title' => 'Noticias',
                'url_prefix' => '/noticias/',
            ],
            'entradas' => [
                'model' => Entrada::class,
                'date_field' => 'created_at',
                'section_title' => 'Blog',
                'url_prefix' => '/blog/',
            ],
            'comunicados' => [
                'model' => Comunicado::class,
                'date_field' => 'fecha_comunicado',
                'section_title' => 'Comunicados',
                'url_prefix' => '/comunicados/',
            ],
            'libros' => [
                'model' => Libro::class,
                'date_field' => 'created_at',
                'section_title' => 'Libros',
                'url_prefix' => '/libros/',
            ]
        ];

        $groups = [];
        $hayContenido = false;

        foreach ($tiposContenido as $key => $info) {
            $items = $info['model']::where($info['date_field'], '>=', $inicio)
                ->where($info['date_field'], '<=', $fin)
                ->get();
            if ($items->isNotEmpty()) {
                $hayContenido = true;
                $g = "## {$info['section_title']}\n";
                foreach ($items as $item) {
                    $url = url($info['url_prefix'] . $item->slug);
                    $g .= "\n\n### [{$item->titulo}]($url)\n\n";
                    $g .= "{$item->descripcion}\n\n";
                }
                $groups[] = $g;
            }
        }

        // Verificar si hay al menos un contenido
        if (!$hayContenido) {
            return response()->json([
                'success' => false,
                'message' => 'No hay contenidos para el periodo solicitado.'
            ], 200);
        }

        $md = "# \n\n¡Hola! Te presentamos los últimos contenidos de Tseyor.\n\n" . implode("\n\n", $groups);
        $html = Markdown::toHtml($md);

        return [
            "titulo" => $titulo,
            "texto" => $html,
            "inicio" => $inicio,
            "fin" => $fin,
            "mes" => $mes,
            "anyo" => $anyo,
            "semana" => $semana ? $semana : 1,
            "tipo" => $tipo,
        ];
    }


    /*
    * Genera el boletín y lo guarda en la base de datos.
    * Manda notificación por correo al usuario revisor, para que lo pueda revisar.
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function prepararBoletin(Request $request)
    {
        // debe haber el parametro tipo, respuesta json
        if(!$request->has('tipo')) {
            return response()->json([
                'success' => false,
                'message' => 'Debes especifica el campo'
            ], 422);
        }

        $data = $this->generarBoletin($request);

        $boletin = Boletin::create([
            'titulo' => $data['titulo'],
            'texto' => $data['texto'],
            'anyo' => $data['anyo'],
            'mes' => $data['mes'],
            'semana' => $data['semana'],
            'tipo' => $data['tipo']
        ]);

        Log::channel('boletines')->info("Se ha preparado el boletín {$boletin->id} ({$boletin->titulo}) de tipo {$boletin->tipo}.");

        // lo notificamos al usuario revisor
        $revisor = config('app.user_revisor_boletines');
        $user = User::where('id', $revisor)->orWhere('name', $revisor)->first();
        if ($user)
            $user->notify(new BoletinGenerado($boletin));
        else
            Log::channel('boletines')->warning("No se ha encontrado el usuario revisor de boletines {$revisor}.");

        return response()->json([
            'success' => true,
            'message' => 'Boletín preparado correctamente.',
            'boletin' => $boletin
        ], 200);
    }

    public function enviarBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);
        $boletin->enviarBoletin();

        return redirect()->back()->with('success', 'Boletín enviado correctamente.');
    }

    /**
     * Function: enviarBoletinesPendientes
     * Description: Envia los boletines pendientes de envío.
     * Condiciones:
     * -tienen que tener al menos 1 día (24 horas) desde su creación, y estar .
     * -estado de enviado not true
     */
    public function enviarBoletinesPendientes(Request $request)
    {
        $horas = config('app.boletin.horas_autoenviar', 24);
        $boletines_pendientes = Boletin::where('enviado', false)->get();

        if ($boletines_pendientes->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No hay boletines pendientes de envío.'
            ], 200);
        }

        $boletines_revision = $boletines_pendientes->filter(function($b) use ($horas) {
            return $b->created_at > now()->subHours($horas);
        });
        $boletines_para_enviar = $boletines_pendientes->filter(function($b) use ($horas) {
            return $b->created_at <= now()->subHours($horas);
        });

        if ($boletines_para_enviar->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Todos los boletines pendientes están en periodo de revisión.'
            ], 200);
        }

        foreach ($boletines_para_enviar as $boletin) {
            $boletin->enviarBoletin();
        }

        return response()->json([
            'success' => true,
            'message' => 'Boletines enviados correctamente. ' .
                ($boletines_revision->isNotEmpty() ? 'Algunos boletines siguen en periodo de revisión.' : ''),
            'enviados' => $boletines_para_enviar->pluck('id'),
            'en_revision' => $boletines_revision->pluck('id'),
        ], 200);
    }
}
