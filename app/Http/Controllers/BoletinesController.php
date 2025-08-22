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
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        // devuelve los boletines recientes según la búsqueda
        $resultados = Boletin::latest()->paginate(BoletinesController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
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
    Log::channel('boletines')->info('Ejecutando generarBoletin', ['request' => $request->all()]);
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
        } elseif ($tipo == 'quincenal') {
            // Cálculo de quincena anterior
            $dia = $hoy->day;
            $mes_actual = $hoy->month;
            $anyo_actual = $hoy->year;
            // Si estamos en la primera quincena del mes, tomamos la segunda quincena del mes anterior
            if ($dia <= 14) {
                $mes_ref = $mes_actual - 1;
                $anyo_ref = $anyo_actual;
                if ($mes_ref < 1) {
                    $mes_ref = 12;
                    $anyo_ref--;
                }
                $inicio = \Carbon\Carbon::create($anyo_ref, $mes_ref, 15, 0, 0, 0);
                $fin = \Carbon\Carbon::create($anyo_ref, $mes_ref, 1, 0, 0, 0)->endOfMonth();
                $titulo = "Boletín Tseyor quincenal: 15 al " . $fin->day . " de " . $fin->translatedFormat('F Y');
            } else {
                // Segunda quincena del mes actual, tomamos la primera quincena del mes actual
                $inicio = \Carbon\Carbon::create($anyo_actual, $mes_actual, 1, 0, 0, 0);
                $fin = \Carbon\Carbon::create($anyo_actual, $mes_actual, 14, 23, 59, 59);
                $titulo = "Boletín Tseyor quincenal: 1 al 14 de " . $inicio->translatedFormat('F Y');
            }
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
             'libros' => [
                'model' => Libro::class,
                'date_field' => 'created_at',
                'section_title' => 'Libros',
                'url_prefix' => '/libros/',
             ],
            'comunicados' => [
                'model' => Comunicado::class,
                'date_field' => 'fecha_comunicado',
                'section_title' => 'Comunicados',
                'url_prefix' => '/comunicados/',
            ]
        ];

        $groups = [];
        $hayContenido = false;

        foreach ($tiposContenido as $key => $info) {
            $items = $info['model']::where($info['date_field'], '>=', $inicio)
                ->where($info['date_field'], '<=', $fin)
                ->get();
            Log::channel('boletines')->info('generarBoletin: Contenidos encontrados', [
                'tipo' => $key,
                'count' => $items->count()
            ]);
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
            Log::channel('boletines')->info('generarBoletin: No hay contenidos para el periodo solicitado.', [
                'inicio' => $inicio,
                'fin' => $fin,
                'tipo' => $tipo
            ]);
            return response()->json([
                'success' => false,
                'message' => 'No hay contenidos para el periodo solicitado.'
            ], 200);
        }

        $md = "# \n\n¡Hola! Te presentamos los últimos contenidos de Tseyor.\n\n" . implode("\n\n", $groups);
        $html = Markdown::toHtml($md);

        Log::channel('boletines')->info('generarBoletin: Boletín generado correctamente.', [
            'titulo' => $titulo,
            'inicio' => $inicio,
            'fin' => $fin,
            'tipo' => $tipo
        ]);

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
    * Espera el parámetro 'tipo', que puede ser semanal, quincenal, mensual o bimestral
    * Si el parámetro 'sendmail' es true, manda notificación por correo al usuario revisor, para que lo pueda revisar.
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function prepararBoletin(Request $request)
    {
        Log::channel('boletines')->info("llamada a prepararBoletin.", ["tipo"=> $request->input("tipo")]);

        // Recoger los parámetros desde el body POST (no query string)
        $tipo = $request->input('tipo');
        $sendmail = $request->input('sendmail', false);

        // debe haber el parametro tipo, respuesta json
        if(!$tipo) {
            Log::channel('boletines')->info("prepararBoletin: no se ha especificado el tipo de boletín.");
            return response()->json([
                'success' => false,
                'message' => 'Debes especificar el campo tipo'
            ], 422);
        }

        // Pasar el request modificado a generarBoletin
        $data = $this->generarBoletin(new Request(['tipo' => $tipo]));

        // comprobar que no exista ya un boletín con el mismo título
        $existe = Boletin::where('titulo', $data['titulo'])->first();
        if ($existe) {
            Log::channel('boletines')->info("prepararBoletin: ya existe un boletín con el mismo título.");
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un boletín con el mismo título.'
            ], 422);
        }

        $boletin = Boletin::create([
            'titulo' => $data['titulo'],
            'texto' => Markdown::toMarkdown($data['texto']), // nos llega el texto en html, lo guardamos en markdown
            'anyo' => $data['anyo'],
            'mes' => $data['mes'],
            'semana' => $data['semana'],
            'tipo' => $data['tipo']
        ]);

        Log::channel('boletines')->info("prepararBoletin: Se ha preparado el boletín {$boletin->id} ({$boletin->titulo}) de tipo {$boletin->tipo}.");

        // si está el parámetro sendmail en true, lo enviamos
        if ($sendmail) {
            $revisor = config('app.user_revisor_boletines');
            $user = User::where('id', $revisor)->orWhere('name', $revisor)->first();
            if ($user) {
                $user->notify(new BoletinGenerado($boletin));
                Log::channel('boletines')->info("prepararBoletin: Se ha notificado al usuario revisor {$user->name} ({$user->email}) del boletín {$boletin->id}.");
            } else {
                Log::channel('boletines')->warning("No se ha encontrado el usuario revisor de boletines {$revisor}.");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Boletín preparado correctamente.',
            'boletin' => $boletin
        ], 200);
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

        Log::channel('boletines')->info("enviarBoletinesPendientes");

        if ($boletines_pendientes->isEmpty()) {
            Log::channel('boletines')->info("enviarBoletinesPendientes: No hay boletines pendientes de envío.");
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
            Log::channel('boletines')->info("enviarBoletinesPendientes: Todos los boletines pendientes están en periodo de revisión.");
            return response()->json([
                'success' => true,
                'message' => 'Todos los boletines pendientes están en periodo de revisión.'
            ], 200);
        }

        foreach ($boletines_para_enviar as $boletin) {
            $boletin->enviarBoletin();
        }

        Log::channel('boletines')->info("enviarBoletinesPendientes: Boletines enviados correctamente.");
        if($boletines_revision->isNotEmpty()) {
            Log::channel('boletines')->info("enviarBoletinesPendientes: Algunos boletines siguen en periodo de revisión.");
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
