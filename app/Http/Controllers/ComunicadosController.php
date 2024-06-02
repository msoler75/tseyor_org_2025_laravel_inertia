<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcesarAudios;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\DiskUtil;

class ComunicadosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     *   https://github.com/codename-12/crud-laravel-with-spatie-permission/blob/master/app/Http/Controllers/PegawaiController.php
     *
     * @return \Illuminate\Http\Response
     */
    /*
    function __construct()
    {
         $this->middleware('permission:pegawai-list|pegawai-create|pegawai-edit|pegawai-delete', ['only' => ['index','show']]);
         $this->middleware('permission:pegawai-create', ['only' => ['create','store']]);
         $this->middleware('permission:pegawai-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:pegawai-delete', ['only' => ['destroy']]);
    }
    */

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $año = $request->input('ano');
        $orden = $request->input('orden');
        $completo = $request->input('completo');
        if ($completo == "0")
            $completo = false;

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = BusquedasHelper::buscar(Comunicado::class, $buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado', 'categoria', 'ano', 'imagen'])
                ->where('visibilidad', 'P');
        }


        // $resultados->where("titulo", "511. Un nuevo enfoque hacia los demas");

        // parámetros
        if (is_numeric($año))
            $resultados = $resultados->where("ano", $año);

        if (is_numeric($categoria))
            $resultados = $resultados->where('categoria', $categoria);

        if (!$orden || $orden == 'recientes')
            $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
        else if ($orden == 'cronologico')
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');

        $resultados = $resultados
            ->paginate(16)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria, 'ano' => $año, 'orden' => $orden, 'completo' => $completo ? 1 : 0]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false, $completo);

        return Inertia::render('Comunicados/Index', [
            'categoria' => $categoria,
            'ano' => $año,
            'orden' => $orden,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'completo' => $completo,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
        ])
            ->withViewData(SEO::get('comunicados'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $comunicado = Comunicado::findOrFail($id);
        } else {
            $comunicado = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $comunicado->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$comunicado || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado,
        ])
            ->withViewData(SEO::from($comunicado));
    }


    /**
     * Genera un PDF desde los datos de un comunicado
     */
    public function pdf($id)
    {
        if (is_numeric($id)) {
            $comunicado = Comunicado::findOrFail($id);
        } else {
            $comunicado = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $comunicado->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$comunicado || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        $nombreArchivo = $comunicado->titulo . ' - TSEYOR.pdf';


        // comprobaremos si existe ya el archivo pdf generado



        $pdf_path = $comunicado->pdfPath; // attribute with accesor


        $pdf_full_path = Storage::disk('public')->path($pdf_path);

        if (file_exists($pdf_full_path) && filemtime($pdf_full_path) > $comunicado->modified_at) {
            return response()->file($pdf_full_path);
        }

        $texto = $comunicado->texto;

        // reducimos las imagenes de los guías para que no sean tan grandes
        $texto = preg_replace_callback("#\!\[\]\(\/almacen\/medios\/guias\/con_nombre\/.+?\.jpg\)\{width=(\d+),height=(\d+)\}#", function ($match) {
            $w = intval($match[1]);
            $h = intval($match[2]);
            $r = $w / $h;
            $h = 250;
            $w = $h * $r;
            return str_replace("width=" . $match[1] . ",height=" . $match[2], "width=$w,height=$h", $match[0]);
        }, $texto);


        $html = \App\Pigmalion\Markdown::toHtml($texto);

        $html = "<style>img{max-width: 100%} p{font-family: Calibri, sans-serif;}table,tr,td,th{border: 0}</style>" . $html;

        // envolver cada img (que está solo en una linea) en un div con style="text-align: center"
        $html = preg_replace_callback('/<img.*?>/', function ($matches) {
            return '<div style="text-align: center">' . $matches[0] . '</div>';
        }, $html);



        // reemplazar todas las imagenes sus rutas relativas con rutas absolutas de disco
        $html = preg_replace_callback('/<img([^>]+)src="([^"]+)"/', function ($matches) {

            $fullpath = DiskUtil::getRealPath($matches[2]);
            $prefix = ""; // "file://";
            // $r = '<img' . $matches[1] . 'src="' . $prefix.$fullpath .'"';
            $r = '<img' . $matches[1] . 'src="data:image/png;base64,' . base64_encode(file_get_contents($fullpath)) . '"';
            return $r;
        }, $html);

        // Contenido HTML completo con etiquetas <html>, <head> y <body>
        $pdf = Pdf::loadView('comunicado-pdf', [
            'titulo' => $comunicado->titulo,
            'texto' => $html,
        ]);

        // guardamos el pdf generado
        DiskUtil::ensureDirExists(dirname($pdf_full_path));
        $pdf->save($pdf_full_path);

        // descargamos el archivo pdf
        return $pdf->download($nombreArchivo);
    }




    /*
        public function archive(Request $request)
        {
            $buscar = $request->input('buscar');

            $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
                ->where('visibilidad', 'P')
                ->latest()->paginate(10)->appends(['buscar' => $buscar]);

            return Inertia::render('Comunicados/Archivo', [
                'listado' => $comunicados
            ]);
        }*/


    public function procesar()
    {
        // comprueba en los comunicados si hay audios que aun no se han convertido, o si hay pdf que no se han preparado con sus metadatos
        $comunicados = Comunicado::where('audios', 'LIKE', '%upload%');

        foreach ($comunicados->get() as $comunicado) {
            if ($comunicado->audios) {
                // dd($comunicado);

                $año = date('Y', strtotime($comunicado->fecha_comunicado));
                $folder = "medios/comunicados/audios/$año";

                dispatch(new ProcesarAudios($comunicado, $folder));
            }
        }
    }
}
