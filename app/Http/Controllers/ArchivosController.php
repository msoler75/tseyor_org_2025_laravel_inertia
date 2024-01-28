<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Nodo;
use App\Models\Acl;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Http\Controllers\ImagenesController;

// use App\Pigmalion\TiempoEjecucion as T;
// use App\Pigmalion\Profiler;

/*
    En sistemas operativos basados en UNIX, como Linux, el sticky bit es un atributo de permisos especial que se puede aplicar a directorios. Este bit tiene un propósito específico y afecta la forma en que los usuarios pueden acceder y manipular archivos dentro de ese directorio.
    Cuando el sticky bit está configurado en un directorio, los usuarios pueden eliminar o renombrar únicamente los archivos que son de su propiedad. Esto significa que, incluso si otros usuarios tienen permisos de escritura en el directorio, no podrán eliminar o renombrar archivos que no sean de su propiedad.
    El propósito principal del sticky bit es asegurar que los usuarios no eliminen o modifiquen archivos de otros usuarios en directorios compartidos, como /tmp. Este directorio suele tener el sticky bit activado para evitar que los usuarios borren o modifiquen archivos de otros usuarios en un entorno multiusuario.
*/

/**
 *
 */
class ArchivosController extends Controller
{

    private function diskRuta(string $ruta): array
    {
        // si la ruta comienza por "archivos", el disco es "archivos"
        // sino, es "public"
        $ruta = $this->normalizarRuta($ruta);
        if ($ruta == '')
            return ['raiz', '']; // raiz

        if (strpos($ruta, 'archivos') === 0) {
            // $ruta = preg_replace("#^archivos\/?#", "", $ruta);
            return ['archivos', $ruta];
        } else if ($ruta == 'mis_archivos') {
            return ['archivos', $ruta];
        } else {
            return ['public', $ruta];
        }
    }

    /**
     * Método por defecto para listar archivos
     */
    public function archivos(Request $request)
    {
        return $this->list($request, $request->path(), false);
    }


    /**
     * Para el gestor de archivos o Media Manager
     */
    public function filemanager(Request $request, $ruta = "/")
    {
        return $this->list($request, $ruta, true);
    }

    /**
     * Listado de una carpeta. Comprueba todos los permisos de acceso
     */
    public function list(Request $request, $ruta, $json)
    {
        // new T("ArchivosController.list($ruta)");

        // $p1 = new T("ArchivosController.list($ruta) P1");

        $ruta = urldecode(ltrim($ruta, '/'));

        if (strpos($ruta, ':') !== false) {
            if ($json) {
                return response()->json(['error' => 'Acceso denegado'], 403);
            }
            abort(403, 'Acceso denegado');
        }

        if (strpos($ruta, '..') !== false) {
            if ($json) {
                return response()->json(['error' => 'No se permiten rutas relativas'], 400);
            }
            abort(400, 'No se permiten rutas relativas');
        }

        list($disk, $ruta) = $this->diskRuta($ruta);

        if ($disk == 'raiz' || $ruta == 'archivos_raiz') {

            $items = [
                [
                    'nombre' => 'raiz',
                    'ruta' => '',
                    'carpeta' => '',
                    'tipo' => 'carpeta',
                    'actual' => true,
                    'tamano' => 0,
                    'archivos' => 0,
                    'subcarpetas' => 2,
                    'url' => '/'
                ],
                [
                    'nombre' => 'archivos',
                    'ruta' => 'archivos',
                    'carpeta' => 'archivos',
                    'tipo' => 'disco',
                    'url' => '/archivos'
                ]
            ];

            if ($ruta == "")
                $items[] =
                    [
                        'nombre' => 'medios',
                        'ruta' => 'medios',
                        'carpeta' => 'medios',
                        'tipo' => 'disco',
                        'url' => '/medios'
                    ];

            if (auth()->user())
                $items[] =
                    [
                        'nombre' => 'mis_archivos',
                        'ruta' => 'mis_archivos',
                        'carpeta' => 'mis_archivos',
                        'tipo' => 'disco',
                        'url' => '/mis_archivos'
                    ];

            $respuesta = [
                'items' => $items,
                'ruta' => $ruta,
                'propietarioRef' => null
            ];

        } else if ($ruta == 'mis_archivos') {
            $items = $this->listMyFiles();

            $respuesta = [
                'items' => $items,
                'ruta' => $ruta,
                'propietarioRef' => null
            ];

        } else {

            // $rutaBase = str_replace($baseUrl, '', Storage::disk($disk)->url($ruta));
            $rutaBase = $ruta;

            // Comprobar si la carpeta existe
            if (!Storage::disk($disk)->exists($rutaBase)) {
                abort(404, 'Ruta no encontrada');
            }

            // si es un archivo, procedemos a la descarga
            if (!Storage::disk($disk)->directoryExists($rutaBase)) {
                // no es una carpeta, así que derivamos a la descarga
                return $this->descargar($request, '/' . /* '/archivos/' . */$rutaBase);
            }

            // unset($p1);
            // $p1b = new T("ArchivosController.list($ruta) P1B");

            // $acl = Acl::from($user, ['ejecutar', 'escribir']);

            // comprobamos el permiso de ejecución (listar) en la carpeta
            $nodo = Nodo::desde($ruta);
            $nodoCarpeta = $nodo;


            // unset($p1b);
            // $p1c = new T("ArchivosController.list($ruta) P1C");

            $user = auth()->user();

            $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

            if (!$esAdministrador && Gate::denies('ejecutar', $nodo)) {
                abort(403, 'No tienes permisos para ver la carpeta');
            }

            // creamos la lista de items que retornaremos
            $items = [];


            // unset($p1c);
            // $p2 = new T("ArchivosController.list($ruta) P2");

            // elementos de la ruta
            $archivos = Storage::disk($disk)->files($ruta);
            $carpetas = Storage::disk($disk)->directories($ruta);

            // unset($p2);
            // $p3 = new T("ArchivosController.list($ruta) P3");

            // agregamos la carpeta actual
            $items[] = $this->prepareItemList($disk, $ruta, $nodo, ['tipo' => 'carpeta', 'actual' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);

            // agregamos carpeta padre
            $padre = dirname($ruta);
            if ($padre == '.')
                $padre = "";
            $nodoPadre = null;
            if ($ruta) {
                $nodoPadre = Nodo::desde($padre);
                $items[] = $this->prepareItemList($disk, $padre, $nodoPadre, ['tipo' => 'carpeta', 'padre' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);
            }
            // dd($ruta, $padre, $items);

            // obtenemos todos los nodos de la carpeta
            // $nodosHijos = Nodo::hijos($ruta);


            // unset($p3);
            // $p4 = new T("ArchivosController.list($ruta) P4 (carpetas)");


            // Agregar carpetas a la colección de elementos
            foreach ($carpetas as $carpeta) {
                $nodo = null; //$nodosHijos->where('ruta', $carpeta /*$ruta . "/" .  basename($carpeta)*/)->first();
                $items[] = $this->prepareItemList(
                    $disk,
                    $carpeta,
                    $nodo,
                    [
                        'tipo' => 'carpeta',
                        'archivos' => count(Storage::disk($disk)->files($carpeta)),
                        'subcarpetas' => count(Storage::disk($disk)->directories($carpeta))
                    ]
                );
            }

            // unset($p4);
            // $p4b = new T("ArchivosController.list($ruta) P4B (archivos)");

            // Agregar archivos a la colección de elementos
            foreach ($archivos as $archivo) {
                $nodo = null; // $nodosHijos->where('ruta', $archivo)->first();
                $items[] = $this->prepareItemList($disk, $archivo, $nodo, [
                    'tipo' => 'archivo',
                    'tamano' => Storage::disk($disk)->size($archivo),
                ]);
            }

            // obtenemos el propietario de la ruta actual, que puede ser un usuario o un grupo/equipo
            $equipo = Equipo::where('group_id', $nodoCarpeta->group_id)->first();
            if ($equipo)
                $propietario = [
                    'url' => route('equipo', $equipo->slug || $equipo->id),
                    'nombre' => $equipo->nombre,
                    'tipo' => 'equipo'
                ];
            else {
                $usuario = User::find($nodoCarpeta->user_id);
                $propietario = [
                    'url' => route('usuario', $usuario->slug || $usuario->id),
                    'nombre' => $usuario->name,
                    'tipo' => 'usuario'
                ];
            }

            // unset($p4b);
            //dd($items);

            // dd(Profiler::results());

            $respuesta = [
                'items' => $items,
                'ruta' => $ruta,
                'propietarioRef' => $propietario
            ];

        }

        if ($json) {
            return response()->json($respuesta, 200);
        }

        return Inertia::render('Archivos', $respuesta)
            ->withViewData([
                'seo' => new SEOData(
                    title: $ruta,
                    description: 'Contenido de ' . $ruta,
                )
            ]);
    }


    /**
     * Devuelve la lista de carpetas del usuario
     */
    public function listMyFiles()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401); // usuario no encontrado
        }

        $nodos = Nodo::de($user->id);

        $items = [
            [
                'nombre' => 'mis_archivos',
                'ruta' => 'mis_archivos',
                'carpeta' => '',
                'tipo' => 'carpeta',
                'actual' => true,
                'tamano' => 0,
                'archivos' => 0,
                'subcarpetas' => 0,
                'url' => route('mis_archivos')
            ]
        ];

        // $nodos->shift();
        foreach ($nodos as $nodo) {
            $ruta = $nodo->ruta;
            list($disk, $ruta) = $this->diskRuta($ruta);
            if (!Storage::disk($disk)->exists($ruta)) {
                $nodo->delete();
                continue;
            }

            if ($nodo->es_carpeta)
                $item = $this->prepareItemList($disk, $nodo->ruta, $nodo, [
                    'tipo' => 'carpeta',
                    'archivos' => count(Storage::disk($disk)->files($nodo->ruta)),
                    'subcarpetas' => count(Storage::disk($disk)->directories($nodo->ruta))
                ]);
            else
                $item = $this->prepareItemList($disk, $nodo->ruta, $nodo, [
                    'tipo' => 'archivo',
                    'tamano' => Storage::disk($disk)->size($nodo->ruta),
                ]);

            $info = $this->prepareItemInfo($nodo->ruta, $item['nombre'], $item['tipo'], $nodo);

            $fields = ['nodo_id', 'puedeEscribir', 'puedeLeer', 'permisos', 'propietario', 'privada'];
            foreach ($fields as $field)
                if (isset($info[$field]))
                    $item[$field] = $info[$field];

            $items[] = $item;
        }


        $ruta = '';

        /* $propietario = [
             'url' => route('usuario', $user->slug || $user->id),
             'nombre' => $user->name,
             'tipo' => 'usuario'
         ]; */

        /* return Inertia::render('ArchivosMios', [
             'items' => $items,
             'ruta' => $ruta,
             'propietarioRef' => $propietario
         ])
             ->withViewData([
                     'seo' => new SEOData(
                         title: 'Mis archivos',
                         description: 'Archivos de ' . $user->name,
                     )
                 ]); */

        return $items;
    }


    /**
     * Obtiene para cada item de items la información de permisos, acl
     */
    private function calc_info($ruta, $items): array
    {
        // comprobamos el permiso de ejecución (listar) en la carpeta
        $nodo = Nodo::desde($ruta);
        $nodoCarpeta = $nodo;

        // agregamos carpeta padre
        $padre = dirname($ruta);
        if ($padre == ".")
            $padre = "";
        $nodoPadre = null;
        if ($padre) {
            $nodoPadre = Nodo::desde($padre);
        }

        // obtenemos todos los nodos de la carpeta
        $nodosHijos = $ruta == 'mis_archivos' ? Nodo::whereRaw("false")->get() : Nodo::hijos($ruta);

        $info = [];

        foreach ($items as $item) {

            $nodo = $item['ruta'] == 'mis_archivos' ? null : $nodosHijos->where('ruta', $item['ruta'])->first();
            if (!$nodo)
                $nodo = Nodo::desde($item['ruta']);

            // dd($nodo);
            $info_item = $this->prepareItemInfo($item['ruta'], $item['nombre'], $item['tipo'] ?? 'archivo', $nodo);

            $info_item['nodo'] = $nodo; // temporal
            // agregamos el item
            $info[$item['nombre']] = $info_item;
        }


        // Obtenemos todos los ids de todos los nodos implicados
        $nodosIdsArr = $ruta == 'mis_archivos' ? [] : $nodosHijos->pluck('id')->toArray();
        if ($nodoCarpeta)
            array_push($nodosIdsArr, $nodoCarpeta->id);
        if ($nodoPadre)
            array_push($nodosIdsArr, $nodoPadre->id);

        // Obtenemos de una vez todos los ACL
        $acl = Acl::inNodes($nodosIdsArr);
        $aclArray = $acl->toArray();

        $user = auth()->user();
        $aclUser = optional($user)->accessControlList();


        // Agregamos la información de Access Control List para cada item
        foreach ($info as $idx => $item) {
            if ($item['nodo_id']) {
                $a = [];
                foreach ($aclArray as $aclItem) {
                    if ($aclItem['nodo_id'] == $item['nodo_id']) {
                        $a[] = $aclItem;
                    }
                }
                foreach ($a as $k => $x) {
                    unset($a[$k]['nodo_id']);
                }
                $info[$idx]['acl'] = $a;
            } else
                $info[$idx]['acl'] = null;

            // agregamos información para saber si podemos editar este item
            // omitimos el item padre
            if (!($item['padre'] ?? 0)) {
                $nodoItem = $item['nodo'];
                $info[$idx]['puedeEscribir'] = $nodoItem ? Gate::allows('escribir', $nodoItem) : false;

                $info[$idx]['puedeLeer'] = $nodoItem ? Gate::allows('leer', $nodoItem) : false;
                $nodoContenedor = $idx === 0 ? $nodoPadre : $nodoCarpeta;
                // comprobamos el sticky bit de la carpeta padre del item
                if ((!$nodoContenedor || $nodoContenedor->sticky) && optional($nodoItem)->user_id != optional($user)->id) {
                    if (!$aclUser || !optional($nodoItem)->tieneAcceso($user, 'escribir'))
                        $info[$idx]['puedeEscribir'] = false;
                }
            }
            // eliminamos la entrada del nodo de los resultados
            unset($info[$idx]['nodo']);
        }

        // $info['_esAdministrador_'] = optional($user)->hasPermissionTo('administrar archivos');

        return $info;
    }

    /**
     * Prepara datos informativos para un item
     */
    private function prepareItemInfo($ruta, $nombre, $tipo, $nodo)
    {
        $info_item = ["nombre" => $nombre, "ruta" => $ruta];
        if (!$nodo || ($tipo == 'carpeta' && Gate::denies('ejecutar', $nodo)))
            $info_item['privada'] = true; // carpeta no accesible
        $info_item['nodo_id'] = optional($nodo)->id ?? null;
        $info_item['permisos'] = optional($nodo)->permisos ?? 0;
        if ($nodo)
            $info_item['propietario'] = [
                'usuario' => ['id' => $nodo->user_id, 'nombre' => $nodo->propietario_usuario],
                'grupo' => ['id' => $nodo->group_id, 'nombre' => $nodo->propietario_grupo]
            ];
        return $info_item;
    }


    /**
     * Obtiene la info de los elementos en la ruta indicada
     */
    public function info(Request $request)
    {
        $ruta = $request->ruta;
        $resp = $this->list($request, $ruta, true); // es un objeto JsonResponse

        //obtenemos los items de la respuesta json
        $items = $resp->original['items'];

        $info = $this->calc_info($ruta, $items);

        return response()->json($info, 200);

    }

    /**
     * Prepara el item de un listado de una carpeta
     */
    private function prepareItemList(string $disk, string $ruta, ?Nodo $nodo, array $options): array
    {
        $ruta = rtrim($ruta, '/');
        // $baseUrl = url('');
        $rutaBase = $ruta; //str_replace($baseUrl, '', str_replace('/almacen', '', Storage::disk($disk)->url($ruta)));
        $prefix = ''; //$disk === 'archivos' ? 'archivos/' : '';
        $carpeta = str_replace("\\", "/", dirname($ruta));
        $item = [
            'nombre' => basename($ruta),
            'ruta' => rtrim($prefix . $rutaBase, '/'),
            // 'url' => Storage::disk($disk)->url(str_replace(' ', '%20', $rutaBase)),
            // 'url' => str_replace($baseUrl, '', str_replace('/almacen', '', Storage::disk($disk)->url($ruta))), // Storage::disk($disk)->url(urldecode($ruta)),
            'carpeta' => $carpeta,
            'fecha_modificacion' => Storage::disk($disk)->lastModified($ruta),
        ];
        $item = array_merge($item, $options);
        $item['url'] = rtrim(($ruta && $disk == 'public' ? '/almacen' : '') . '/' . $prefix . $ruta, '/');
        return $item;
    }

    /**
     * Esta función ajax sirve para buscar progresivamente archivos que tengan o contengan un nombre
     * Primero devuelve una respuesta rápida  a través de consulta de nodos
     * Una vez presentada la primera respuesta rápida, escanea el disco para buscar resultados e irlos devolviendo progresivamente
     * Para esta progresiva entrega de resultados usa una lista de carpetas_pendientes donde recursivamente va haciendo las búsquedas
     */
    public function buscar(Request $request)
    {
        // nombre a buscar
        $nombre = $request->nombre;

        // ruta donde empezar a buscar
        $baseUrl = url('');
        // $ruta = ltrim(str_replace($baseUrl, '', $request->ruta), "/");

        // carpetas donde falta buscar
        $carpetas_pendientes = [];

        // id de busqueda de esta llamada
        $id_busqueda = $request->id_busqueda;

        // estamos en la primera busqueda (es un método GET)
        $id_busqueda_actual = $request->session()->get('id_busqueda') || null;

        $user = auth()->user();
        $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

        // es una nueva búsqueda o el id de busqueda es otro
        if (!$id_busqueda || $id_busqueda != $id_busqueda_actual) {

            $ruta = $request->ruta;

            if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
                return response()->json(['error' => 'Ruta relativa no permitida'], 400);
            }

            list($disk, $ruta) = $this->diskRuta($ruta);

            // la ruta inicial es la primera carpeta donde buscaremos después en disco
            $carpetas_pendientes = [$ruta];

            Log::info("Nueva busqueda en $ruta. Buscando $nombre ...");
            Log::info("carpetas_pendientes", $carpetas_pendientes);

            // realizamos una rápida búsqueda inicial, usando nodos
            $nodos = Nodo::search($nombre)->query(function ($query) use ($ruta) {
                return $query->whereRaw("ruta LIKE '$ruta%'");
            })->take(50)->get();

            $resultados = [];

            foreach ($nodos as $nodo) {
                Log::info("nodo encontrado: " . $nodo->id . " - " . $nodo->nombre . " - " . $nodo->ruta);
                if (!Storage::disk($disk)->exists($nodo->ruta)) {
                    Log::info("no existe archivo o carpeta");
                    continue;
                }

                $acceso = $esAdministrador;

                if (!$acceso) {
                    // comprovar visibilidad, miramos la carpeta padre
                    $nodoPadre = Nodo::desde(dirname($nodo->ruta));
                    $acceso = $nodo && Gate::allows('ejecutar', $nodoPadre);
                }

                if (!$acceso) {
                    continue;
                }

                if ($nodo->es_carpeta)
                    $resultados[] = $this->prepareItemList($disk, $nodo->ruta, $nodo, [
                        'tipo' => 'carpeta',
                        'archivos' => count(Storage::disk($disk)->files($nodo->ruta)),
                        'subcarpetas' => count(Storage::disk($disk)->directories($nodo->ruta))
                    ]);
                else
                    $resultados[] = $this->prepareItemList($disk, $nodo->ruta, $nodo, [
                        'tipo' => 'archivo',
                        'tamano' => Storage::disk($disk)->size($nodo->ruta),
                    ]);
            }

            // guardamos las carpetas pendientes en la sesión:
            $request->session()->put('carpetas_pendientes', $carpetas_pendientes);

            Log::info("guardamos carpetas_pendientes", $carpetas_pendientes);

            // generamos un id de busqueda
            $id_busqueda_actual = uniqid();

            $request->session()->put('id_busqueda', $id_busqueda_actual);
            $request->session()->put('buscar_ruta', $ruta);

        } else {

            // continuación de búsqueda, ahora en el sistema de archivos
            Log::info("continuacion de busqueda");

            // recuperamos los parámetros de búsqueda de la sesión
            $carpetas_pendientes = $request->session()->get('carpetas_pendientes');
            $nombre = $request->session()->get('buscar_nombre');
            $ruta = $request->session()->get('buscar_ruta');

            Log::info("recuperamos nombre: $nombre, ruta: $ruta");
            Log::info("recuperamos carpetas_pendientes", $carpetas_pendientes);

            list($disk, $ruta) = $this->diskRuta($ruta);

            // realizamos una busqueda real en disco

            $start_time = microtime(true);
            $tiempo_transcurrido = 0;
            $resultados = [];

            // tiempo máximo de búsqueda: 900 milisegundos
            while (count($carpetas_pendientes) > 0 && $tiempo_transcurrido < 900) {
                // Obtener la primera carpeta pendiente para procesar
                $carpeta = array_shift($carpetas_pendientes);
                if (!$carpeta)
                    continue;
                $carpeta = ltrim(str_replace($baseUrl, '', $carpeta), "/");
                //if (!preg_match("/^archivos/", $carpeta))
                //  continue;

                // Buscar archivos y subcarpetas dentro de la carpeta actual
                $archivos = Storage::disk($disk)->files($carpeta);
                $subcarpetas = Storage::disk($disk)->directories($carpeta);

                // Comprobar si algún archivo tiene un nombre similar a la cadena de búsqueda
                foreach ($archivos as $archivo) {
                    Log::info("archivo $archivo");
                    if ($this->matchSearch(basename($archivo), $nombre))
                        $resultados[] = $this->prepareItemList($disk, $archivo, null, [
                            'tipo' => 'archivo',
                            'tamano' => Storage::disk($disk)->size($archivo)
                        ]);
                }

                // Comprobar si alguna subcarpeta tiene un nombre similar a la cadena de búsqueda
                foreach ($subcarpetas as $subcarpeta) {
                    $item = null;

                    Log::info("carpeta $subcarpeta");

                    if ($this->matchSearch(basename($subcarpeta), $nombre)) {
                        $item = $this->prepareItemList($disk, $subcarpeta, null, [
                            'tipo' => 'carpeta',
                            'archivos' => count(Storage::disk($disk)->files($subcarpeta)),
                            'subcarpetas' => count(Storage::disk($disk)->directories($subcarpeta))
                        ]);
                        $resultados[] = $item;
                    }

                    // miramos si podemos buscar en esta carpeta
                    $acceso = $esAdministrador;
                    if (!$acceso) {
                        $nodo = Nodo::desde($ruta);
                        $acceso = $nodo && Gate::allows('ejecutar', $nodo);
                    }

                    $item['privada'] = !$acceso; // carpeta accesible?

                    // para que se procese en la siguiente iteración del bucle
                    if ($acceso)
                        $carpetas_pendientes[] = $subcarpeta;
                }

                // Calcular el tiempo transcurrido en milisegundos
                $tiempo_transcurrido = (microtime(true) - $start_time) * 1000;

                // guardamos los cambios de las carpetas pendientes en la sesión:
                $request->session()->put('carpetas_pendientes', $carpetas_pendientes);

                Log::info("guardamos carpetas_pendientes", $carpetas_pendientes);
            }
        }

        $response = [
            'finalizado' => !count($carpetas_pendientes),
            'resultados' => $resultados
        ];

        if ($id_busqueda_actual)
            $response['id_busqueda'] = $id_busqueda_actual;

        return response()->json($response, 200);

    }


    /**
     * Fuzzy match file names
     */
    private function matchSearch($str, $term)
    {
        $str = Str::lower(Str::ascii($str));
        $term = Str::lower(Str::ascii($term));
        if (strpos($term, ".") === false) // removemos la extension del archivo
            $str = preg_replace("/\.[^.]{2,8}$/", "", $str);
        if (str_contains($str, $term))
            return true;
        return levenshtein($term, $str, 1, 3, 4) < 7;
    }



    /**
     * Acceso público al almacen de medios
     */
    public function almacen(string $ruta)
    {
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        list($disk, $ruta) = $this->diskRuta($ruta);

        if ($disk != 'public')
            abort(403, 'No tienes permisos.');

        if ($disk == 'public' && Storage::disk($disk)->directoryExists($ruta))
            abort(403, 'Acceso no permitido.');

        $path = Storage::disk($disk)->path($ruta);
        $mime = Storage::disk($disk)->mimeType($ruta);

        return response()->file($path, ['Content-Type' => $mime]);
    }

    /**
     * Controla el acceso a las descargas
     */
    public function descargar(Request $request, string $ruta)
    {
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        list($disk, $ruta) = $this->diskRuta($ruta);

        // no se puede descargar una carpeta
        if (Storage::disk($disk)->directoryExists($ruta))
            abort(403, 'Acceso no permitido.');

        if (!Storage::disk($disk)->exists($ruta))
            abort(404);

        $user = auth()->user();
        $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

        if ($disk != 'public') {
            // obtenemos el nodo correspondiente
            $nodo = Nodo::desde($ruta);

            // comprobamos permisos de lectura
            if (!$esAdministrador && Gate::denies('leer', $nodo))
                abort(403, 'No tienes permisos.');
        }

        $mime = Storage::disk($disk)->mimeType($ruta);
        $path = Storage::disk($disk)->path($ruta);

        // si es una imagen
        if($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif') {
            $controller =new ImagenesController();
            return $controller->descargar($request, $path);
        }


        return response()->file($path, ['Content-Type' => $mime]);
        // return response()->download($path);
    }




    ////////////////////////////////////////////////////////////////////
    ///// API
    ////////////////////////////////////////////////////////////////////


    /**
     * Procesa la subida de archivos
     * Se exige una carpeta (folder) destino, el archivo quedará en storage/app/public/$folder
     */
    public function processUpload(Request $request, UploadedFile $file, string $folder)
    {
        if (!$file) {
            return response()->json([
                'error' => 'noFileGiven'
            ], 400);
        }

        if (!$folder) {
            return response()->json([
                'error' => 'noDestinationPathGiven'
            ], 400);
        }

        $deniedTypes = ['exe', 'cmd', 'php', 'js', 'htaccess'];

        if (in_array(strtolower($file->getClientOriginalExtension()), $deniedTypes)) {
            return response()->json([
                'error' => 'Archivo no permitido'
            ], 415);
        }


        $max_upload_size = config('filesystems.max_upload_size');
        if ($file->getSize() > $max_upload_size) {
            return response()->json([
                'error' => 'Archivo demasiado grande'
            ], 413);
        }

        $ofolder = $folder;
        list($disk, $folder) = $this->diskRuta($folder);
        //dd($disk, $folder);
        // $disk = 'public';

        $path = Storage::disk($disk)->path($folder);
        // dd($path);
        Log::info("uploading file '$path' in disk $disk, original folder: $ofolder, current folder: $folder");

        // Crear la carpeta si no existe
        // $path = storage_path("app/" . $folder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Verificar si el archivo ya existe en la carpeta
        $counter = 1;
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        $newFilename = $filename;
        $folderPath = $path; //storage_path("app/public/" . $folder);

        while (File::exists($folderPath . '/' . $newFilename)) {
            $newFilename = $baseFilename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $filename = $newFilename;
        $storedPath = $file->storeAs($folder, $filename, $disk);

        // creamos su nodo
        Nodo::crear($folder . '/' . $filename, false, auth()->user());

        // Obtener la URL pública del archivo
        $baseUrl = Storage::disk($disk)->url('');
        $url = Storage::disk($disk)->url($storedPath);
        $url = str_replace($baseUrl, '', $url);


        return response()->json([
            'data' => [
                'filePath' => $url
            ]
        ], 200);
    }

    // viene de archivos
    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        $folder = $this->normalizarRuta($request->destinationPath);


        return $this->processUpload($request, $file, $folder);
    }

    // viene del markdown editor
    public function uploadImage(Request $request)
    {
        $file = $request->file('image');
        if (!$file)
            $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'error' => 'noFileGiven'
            ], 400);
        }

        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedTypes)) {
            return response()->json([
                'error' => 'typeNotAllowed'
            ], 415);
        }

        $folder = $this->normalizarRuta($request->destinationPath);
        // detecta si estamos editando un tipo de datos y lo extrae, para asignarle después una carpeta
        /* $url = $request->headers->get('referer');
    if ($url && preg_match('/admin\/(.*?)\/\d+\/edit/', $url, $matches)) {
        $folder = $matches[1];
    } else {
        // $folder = null;
    } */
        return $this->processUpload($request, $file, $folder);
    }



    function validarNombre($nombre)
    {
        // Expresión regular para Windows
        $patronWindows = '/^[a-zA-Z0-9\s_\-().\[\]{}!,@áéíóúÁÉÍÓÚàèòÀÈÒçÇ]*$/';

        // Expresión regular para Linux
        $patronLinux = '/^[a-zA-Z0-9\s_.\-áéíóúÁÉÍÓÚàèòÀÈÒçÇ]*$/';

        // $patronAmbos = '^[a-zA-Z0-9\s_\-().\[\]{}!,@áéíóúÁÉÍÓÚàèòÀÈÒüÜñÑçÇ]*$';

        // Verificar según el sistema operativo
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return preg_match($patronWindows, $nombre);
        } else {
            return preg_match($patronLinux, $nombre);
        }
    }

    /**
     * Crea una carpeta
     */
    public function makeDir(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $folder = $this->normalizarRuta($request->folder);
        $name = $request->name;

        if (!$folder) {
            return response()->json([
                'error' => 'noFolderGiven'
            ], 400);
        }

        if (strpos($folder, '..') !== false) {
            return response()->json([
                'error' => 'dotsNotAllowed'
            ], 400);
        }

        if (!$name) {
            return response()->json([
                'error' => 'noNameGiven'
            ], 400);
        }

        list($disk, $folder) = $this->diskRuta($folder);

        $folderPath = Storage::disk($disk)->path($folder);

        if (!file_exists($folderPath)) {
            return response()->json([
                'error' => 'folderNotFound'
            ], 404);
        }

        $newFolderPath = $folderPath . '/' . $name;

        if (file_exists($newFolderPath)) {
            return response()->json([
                'error' => 'folderAlreadyExists'
            ], 409);
        }


        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // comprobamos los permisos de escritura
        //$acl = Acl::from($user);
        if (!$esAdministrador) {
            $nodo = Nodo::desde($folder);
            if (!$nodo || Gate::denies('escribir', $nodo)) {
                return response()->json([
                    'error' => 'No tienes permisos'
                ], 403);
            }
        }

        if (!$this->validarNombre($name)) {
            return response()->json([
                'error' => 'El nombre de la carpeta no es válido. Solo se permiten caracteres alfanúmericos, espacios, guiones y puntos'
            ], 400);
        }

        // creamos la carpeta
        if (!@mkdir($newFolderPath, 0755)) {
            return response()->json([
                'error' => 'No se ha podido crear la carpeta. Por favor consulta con el administrador'
            ], 500);
        }

        // Creamos el nodo de la carpeta
        Nodo::crear($folder . '/' . $name, true, $user);

        return response()->json([
            'message' => 'folderCreated'
        ], 200);
    }


    /**
     * Elimina un item, indicado en ruta
     * retorna JSON
     */
    public function delete($ruta)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Concatenar la ruta completa al archivo
        $archivo = $ruta; // str_replace('/almacen', '', $ruta);

        // Verificar si la ruta contiene saltos de carpeta
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        list($disk, $ruta) = $this->diskRuta($ruta);

        // Verificar que el archivo exista
        if (!Storage::disk($disk)->exists($archivo)) {
            return response()->json(['error' => 'El archivo no existe'], 404);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // comprobamos los permisos de escritura
        if (!$esAdministrador) {
            $nodoItem = Nodo::desde($ruta);
            if (!$nodoItem || Gate::denies('escribir', $nodoItem)) {
                return response()->json([
                    'error' => 'No tienes permisos'
                ], 403);
            }
        }

        // comprobamos sticky bit y acl
        $nodoContenedor = Nodo::desde(dirname($ruta));
        if ($nodoContenedor->sticky && $nodoItem->user_id != $user->id) {
            if (!$nodoItem->tieneAcceso($user, 'escribir'))
                return response()->json([
                    'error' => 'No tienes permisos de propietario'
                ], 403);
        }

        // Verificar si la ruta es una carpeta
        if (Storage::disk($disk)->directoryExists($archivo)) {
            // Verificar si la carpeta está vacía antes de eliminarla
            if (count(Storage::allFiles($archivo)) > 0) {
                return response()->json(['error' => 'No se puede eliminar la carpeta porque no está vacía'], 400);
            }

            // Eliminar la carpeta vacía
            if (Storage::deleteDirectory($archivo)) {
                return response()->json(['message' => 'Carpeta eliminada correctamente'], 200);
            } else {
                return response()->json(['error' => 'No se pudo eliminar la carpeta'], 500);
            }
        }

        // Intentar eliminar el archivo
        else if (Storage::disk($disk)->delete($archivo)) {
            return response()->json(['message' => 'Archivo eliminado correctamente'], 200);
        } else {
            return response()->json(['error' => 'No se pudo eliminar el archivo'], 500);
        }
    }



    /**
     * Cambia los permisos de un nodo
     * parámetros: ruta, user_id, group_id, permisos
     * Para hacer este cambio has de ser el propietario del nodo, o ser superadmin
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // cambia los permisos
        $permisos = $request->permisos;

        $ruta = $request->ruta;
        list($disk, $ruta) = $this->diskRuta($ruta);

        if (!Storage::disk($disk)->exists($ruta)) {
            return response()->json(['error' => "La ruta '$ruta' no existe"], 404);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // se requiere permisos de escritura en el nodo
        if (!$esAdministrador) {
            $nodoItem = Nodo::desde($ruta);
            if (!$nodoItem || $nodoItem->user_id != $user->id) {
                // El usuario tiene el permiso "administrar archivos/*Gate::denies('escribir', $nodoItem)*/) {
                return response()->json([
                    'error' => 'No tienes permisos'
                ], 403);
            }
        }

        // comprobamos sticky bit y acl
        $nodoContenedor = Nodo::desde(dirname($ruta));
        if ($nodoContenedor->sticky && $nodoItem->user_id != $user->id) {
            if (!$nodoItem->tieneAcceso($user, 'escribir'))
                return response()->json([
                    'error' => 'No tienes permisos de propietario'
                ], 403);
        }

        $esCarpeta = Storage::disk($disk)->directoryExists($ruta);

        $update = [];
        if ($permisos)
            $update['permisos'] = $permisos;
        if ($request->user_id)
            $update['user_id'] = $request->user_id;
        if ($request->group_id)
            $update['group_id'] = $request->group_id;

        // queremos un nodo de esta ruta, no heredado
        $nodo = Nodo::where('ruta', $ruta)->first();
        if (!$nodo) {
            $nodo = Nodo::create([
                'ruta' => $ruta,
                'es_carpeta' => $esCarpeta,
                'user_id' => $update['user_id'] ? $update['user_id'] : $nodoItem->user_id,
                'group_id' => $update['group_id'] ? $update['group_id'] : $nodoItem->group_id,
                'permisos' => $update['permisos'] ? $update['permisos'] : $nodoItem->permisos,
            ]);
        } else {
            if (count($update))
                $nodo->update($update);
        }

        $item = $this->prepareItemList($disk, $ruta, $nodo, ['tipo' => Storage::disk($disk)->directoryExists($ruta) ? 'carpeta' : 'archivo']);

        $newAcls = $request->acl;
        if ($newAcls) {
            foreach ($newAcls as $idx => $newAcl) {
                // buscamos los acl para saber si son nuevos o conviene actualizarlos
                if ($newAcl['id'] < 0) {
                    // es un nuevo acceso
                    unset($newAcl['id']);
                    $newAcl['nodo_id'] = $nodo->id;
                    // creamos un nuevo registro
                    $acl = Acl::create($newAcl);
                    $newAcls[$idx]['id'] = $acl->id;  //actualizamos el id del nuevo acl con el creado en la tabla
                } else {
                    // actualizamos el acl
                    $acl = Acl::where('id', $newAcl['id'])->first();
                    if ($acl->verbos != $newAcl['verbos'])
                        $acl->update(['verbos' => $newAcl['verbos']]);
                }
            }

            // eliminamos los acl que no están en el update
            $aclsCheck = Acl::where('nodo_id', '=', $nodo->id)->get();
            foreach ($aclsCheck as $aclCheck) {
                $encontrado = false;
                foreach ($newAcls as $newAcl) {
                    if ($newAcl['id'] == $aclCheck->id)
                        $encontrado = true;
                }
                if (!$encontrado)
                    $aclCheck->delete();
            }

            $acls = Acl::inNodes([$nodo->id]);
            if ($nodo->id) {
                $a = $acls->where('nodo.id', '=', $nodo->id)->toArray();
                foreach ($a as $k => $x) {
                    unset($a[$k]['nodo']);
                    unset($a[$k]['nodo_id']);
                }
                $item['acl'] = array_values($a);
            }
        }


        $info = $this->calc_info($ruta, [$item]);
        if (count($info) == 1)
            // obtener el primer valor del array
            $info = array_shift($info);

        $fields = ['nodo_id', 'puedeEscribir', 'puedeLeer', 'permisos', 'propietario', 'privada'];
        foreach ($fields as $field)
            if (isset($item[$field]))
                $item[$field] = $info[$field];

        return response()->json($item, 200);
    }




    private function safe_rename($source, $destination)
    {

        Log::info("safe_rename($source, $destination)");

        // Verifica si el origen es un archivo
        if (is_file($source)) {
            // Renombra el archivo
            if (@rename($source, $destination)) {
                return true;
            } else {
                return false;
            }
        }

        // Verifica si el origen es una carpeta
        if (is_dir($source)) {

            if (@rename($source, $destination)) {
                return true;
            } else {


                // Crea la carpeta de destino
                if (!@mkdir($destination)) {
                    return false;
                }

                // Abre el directorio de origen
                $dir = opendir($source);

                // Recorre los elementos del directorio
                while (($file = readdir($dir)) !== false) {
                    if ($file != '.' && $file != '..') {
                        // Construye las rutas completas de origen y destino
                        $src = $source . '/' . $file;
                        $dst = $destination . '/' . $file;

                        // Llama a la función de forma recursiva
                        if (!$this->safe_rename($src, $dst)) {
                            closedir($dir);
                            return false;
                        }
                    }
                }

                // Cierra el directorio de origen
                closedir($dir);

                // Elimina la carpeta de origen
                if (!@rmdir($source)) {
                    return false;
                }
            }

            return true;
        }

        return false; // Si no es ni archivo ni carpeta
    }



    /**
     * Renombra un archivo o carpeta que está en una carpeta $folder de viejo nombre $oldName a $newName
     * parámetros: folder (carpeta donde está el nodo), oldName (nombre actual del nodo), newName (nuevo nombre del nodo)
     * @return \Illuminate\Http\Response JSON
     */
    public function rename(Request $request)
    {

        function rename_win($oldfile, $newfile)
        {
            if (!@rename($oldfile, $newfile)) {
                if (copy($oldfile, $newfile)) {
                    unlink($oldfile);
                    return TRUE;
                }
                return FALSE;
            }
            return TRUE;
        }


        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $ruta = $this->normalizarRuta($request->folder);
        $oldName = $request->oldName;
        $newName = $request->newName;
        //  dd("folder=$folder  oldName=$oldName  newName=$newName");

        // Verificar si faltan parámetros
        if (!$request->filled(['folder', 'oldName', 'newName'])) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        list($disk, $ruta) = $this->diskRuta($ruta);

        $rutaAntes = $ruta . '/' . $oldName;
        $rutaDespues = $ruta . '/' . $newName;
        $itemAntes = $rutaAntes;
        $itemDespues = $rutaDespues;

        // dd("itemAntes=$itemAntes itemDespues=$itemDespues");

        // Verificar que el item exista
        if (!Storage::disk($disk)->exists($itemAntes)) {
            return response()->json(['error' => "El elemento '$itemAntes' no existe"], 404);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        if (!$esAdministrador) {
            // se requiere: permisos de escritura en la carpeta contenedora del item, y permisos de escritura en el item
            $nodoContenedor = Nodo::desde($ruta);
            $nodoItem = Nodo::desde($rutaAntes);
            if (!$nodoContenedor || !$nodoItem || Gate::denies('escribir', $nodoContenedor) || Gate::denies('escribir', $nodoItem)) {
                return response()->json([
                    'error' => 'No tienes permisos de escritura'
                ], 403);
            }

            // comprobamos sticky bit y acl
            if ($nodoContenedor->sticky && $nodoItem->user_id != $user->id) {
                if (!$nodoItem->tieneAcceso($user, 'escribir'))
                    return response()->json([
                        'error' => 'No tienes permisos de propietario'
                    ], 403);
            }
        }

        if (!$this->validarNombre($newName)) {
            return response()->json([
                'error' => 'El nuevo nombre no es válido. Solo se permiten caracteres alfanúmericos, espacios, guiones y puntos'
            ], 400);
        }


        $rutaAbsolutaAntes = realpath(Storage::disk($disk)->path($rutaAntes));
        $rutaAbsolutaDespues = preg_replace("/[\/\\\\]/", DIRECTORY_SEPARATOR, Storage::disk($disk)->path($rutaDespues));

        //Log::info("Laravel move($itemAntes, $itemDespues) disk=$disk");
        Log::info("rename($rutaAbsolutaAntes, $rutaAbsolutaDespues) disk=$disk");
        // Intentar renombrar el item
        //if (Storage::disk($disk)->move($itemAntes, $itemDespues)) {
        /*if (rename_win($rutaAbsolutaAntes, $rutaAbsolutaDespues)) {
        $response = response()->json(['message' => 'Se ha aplicado el nuevo nombre'], 200);
    } else {
        //if(Storage::disk($disk)->directoryExists($itemAntes))
        return response()->json(['error' => 'No se pudo renombrar'], 500);
    }*/
        if ($this->safe_rename($rutaAbsolutaAntes, $rutaAbsolutaDespues)) {
            //if(File::move(Storage::disk($disk)->path($rutaAntes), Storage::disk($disk)->path($rutaDespues) )){
            $response = response()->json(['message' => 'Se ha aplicado el nuevo nombre'], 200);
        } else {
            return response()->json(['error' => 'No se pudo renombrar'], 500);
        }

        // debemos renombrar todas las rutas afectadas en los nodos
        Nodo::mover($rutaAntes, $rutaDespues);

        return $response;
    }


    /**
     * Mueve un conjunto de archivos a otra carpeta
     * @param string sourceFolder
     * @param string destinationFolder
     * @param array items
     * @return \Illuminate\Http\Response JSON
     */
    public function move(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $sourceFolder = $request->sourceFolder;
        $destinationFolder = $request->targetFolder;

        if (!$sourceFolder || !$destinationFolder) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        if (strpos($sourceFolder, '..') !== false || strpos($destinationFolder, '..') !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        list($diskSource, $sourceFolder) = $this->diskRuta($sourceFolder);
        list($diskDest, $destinationFolder) = $this->diskRuta($destinationFolder);

        /*if ($disk1 != $disk2) {
            return response()->json(['error' => 'No se permite mover entre discos'], 403);
        }*/

        // $disk = $disk1;

        $items = $request->items;

        if (!is_array($items)) {
            return response()->json(['error' => 'Los elementos no son un array'], 400);
        }

        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // dd($nodoSource);

        if (!$esAdministrador) {
            $nodoSource = Nodo::desde($sourceFolder);
            $nodoDestination = Nodo::desde($destinationFolder);

            if (!$nodoSource || Gate::denies('leer', $nodoSource)) {
                return response()->json([
                    'error' => 'No tienes permisos de lectura en la carpeta origen'
                ], 403);
            }
            if (!$nodoSource || Gate::denies('escribir', $nodoSource)) {
                return response()->json([
                    'error' => 'No tienes permisos de escritura en la carpeta origen'
                ], 403);
            }
            if (!$nodoDestination || Gate::denies('escribir', $nodoDestination)) {
                return response()->json([
                    'error' => 'No tienes permisos en la carpeta destino'
                ], 403);
            }
        }


        // Mover cada item a la nueva carpeta de destino
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($items as $item) {
            $rutaAntes = $sourceFolder . "/" . $item;
            $rutaDespues = $destinationFolder . "/" . $item;
            $itemSource = $rutaAntes;
            $itemDestination = $rutaDespues;

            // Comprobamos sticky bit (si está activado no podemos mover archivos o carpetas que no son nuestros)
            if (!$esAdministrador && $nodoSource->sticky) {
                $nodoItem = Nodo::desde($rutaAntes);
                if ($nodoItem->user_id != $user->id) {
                    $errorCount++;
                    $errorMessages[] = "El item '$itemSource' no se pudo mover";
                    continue;
                }
            }

            // Verificar que el item exista
            if (!Storage::disk($diskSource)->exists($itemSource)) {
                $errorCount++;
                $errorMessages[] = "El item '$itemSource' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if (Storage::disk($diskSource)->directoryExists($itemSource)) {
                // Intentar mover la carpeta
                if (File::moveDirectory(Storage::disk($diskSource)->path($rutaAntes), Storage::disk($diskDest)->path($rutaDespues))) {
                    // if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Carpeta '$item' movida de '$sourceFolder' a '$destinationFolder'");

                    // cambias los nodos afectados
                    Nodo::mover($rutaAntes, $rutaDespues);
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo mover la carpeta '$itemSource'";
                }
            } else {
                // Verificar si el archivo de destino ya existe
                $counter = 1;
                while (Storage::disk($diskDest)->exists($itemDestination)) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $rutaDespues = $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $itemDestination = $rutaDespues;
                    $counter++;
                }

                if (File::move(Storage::disk($diskSource)->path($rutaAntes), Storage::disk($diskDest)->path($rutaDespues))) {
                    // if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Archivo '$item' movido de '$sourceFolder' a '$destinationFolder'");

                    // debemos renombrar todas las rutas afectadas en los nodos
                    Nodo::mover($rutaAntes, $rutaDespues);
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo mover el archivo '$itemSource'";
                }
            }
        }

        if ($successCount > 0) {
            $message = $successCount == 1 ? '1 elemento movido correctamente' : $successCount . ' elementos movidos correctamente';
            $response = ['message' => $message];
        } else {
            $response = ['error' => 'No se pudo mover ningún elemento'];
        }

        if ($errorCount > 0) {
            $response['errors'] = $errorMessages;
        }

        // Agregar registro de resumen a archivo de log
        Log::info("$successCount elementos movidos correctamente y $errorCount elementos fallidos");

        return response()->json($response, $successCount > 0 ? 200 : 500);
    }

    /*
        private function copy_storage($from, $to, $directory = '/')
        {
            foreach (Storage::disk($from)->files($directory) as $file) {
                if (Storage::disk($to)->exists($file) && Storage::disk($to)->size($file) != Storage::disk($from)->size($file)) {
                    Storage::disk($to)->delete($file);
                }
                if (! Storage::disk($to)->exists($file)) {
                    Storage::disk($to)->writeStream($file, Storage::disk($from)->readStream($file));
                    echo "Copied: $file\n";
                }
            }
            foreach (Storage::disk($from)->directories($directory) as $dir) {
                $this->copy_storage($from, $to, $dir);
            }
        }
    */

    /**
     * Copia un conjunto de archivos a otra carpeta
     */
    public function copy(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $sourceFolder = $request->sourceFolder;
        $destinationFolder = $request->targetFolder;

        if (!$sourceFolder || !$destinationFolder) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        if (strpos($sourceFolder, '..') !== false || strpos($destinationFolder, '..') !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        list($diskSource, $sourceFolder) = $this->diskRuta($sourceFolder);
        list($diskDest, $destinationFolder) = $this->diskRuta($destinationFolder);

        /* if ($disk1 != $disk2) {
            return response()->json(['error' => 'No se permite mover entre discos'], 403);
        } */

        // $disk = $diskSource;

        $items = $request->items;

        if (!is_array($items)) {
            return response()->json(['error' => 'Los elementos no son un array'], 400);
        }

        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        if (!$esAdministrador) {
            $nodoSource = Nodo::desde($sourceFolder);
            $nodoDestination = Nodo::desde($destinationFolder);
            if (!$nodoSource || Gate::denies('leer', $nodoSource /*,  $acl*/)) {
                return response()->json([
                    'error' => 'No tienes permisos para leer los archivos'
                ], 403);
            }

            if (!$nodoDestination || Gate::denies('escribir', $nodoDestination)) {
                return response()->json([
                    'error' => 'No tienes permisos para escribir'
                ], 403);
            }
        }

        // Copiar cada item a la nueva carpeta de destino
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($items as $item) {
            $itemSource = $sourceFolder . "/" . $item;
            $itemDestination = $destinationFolder . "/" . $item;

            // Verificar que el item exista
            if (!Storage::disk($diskSource)->exists($itemSource)) {
                $errorCount++;
                $errorMessages[] = "El elemento '$itemSource' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if (Storage::disk($diskSource)->directoryExists($itemSource)) {
                // Intentar copiar la carpeta
                // if($diskSource == $diskDest)
                //   $result =  Storage::disk($disk)->copyDirectory($itemSource, $itemDestination);
                //else {
                $result = File::copyDirectory(Storage::disk($diskSource)->path($itemSource), Storage::disk($diskDest)->path($itemDestination));
                //}
                if ($result) {
                    $successCount++;
                    // Agregar registro de copia a archivo de log
                    Log::info("Carpeta '$item' copiada de '$sourceFolder' a '$destinationFolder'");
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar la carpeta '$itemSource'";
                }
            } else {
                // Verificar si el archivo de destino ya existe, en tal caso le añadiremos un sufijo _n
                $counter = 1;
                while (Storage::disk($diskDest)->exists($itemDestination)) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $itemDestination = $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $counter++;
                }

                // copia en un mismo disco
                //if($diskSource == $diskDest)
                //  $result = Storage::disk($diskSource)->copy($itemSource, $itemDestination);
                //else {
                // copia entre discos
                //  $content = Storage::disk($diskSource)->get($itemSource);
//                    $result = Storage::disk($diskDest)->put($itemDestination, $content);
                //              }

                $result = File::copy(Storage::disk($diskSource)->path($itemSource), Storage::disk($diskDest)->path($itemDestination));

                if ($result) {
                    $successCount++;
                    Log::info("Archivo '$item' copiado de '$sourceFolder' a '$destinationFolder'");
                } else {
                    // Agregar registro de copia a archivo de log
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar el archivo '$itemSource'";
                }
            }
        }

        if ($successCount > 0) {
            $message = $successCount == 1 ? '1 elemento copiado correctamente' : $successCount . ' elementos copiados correctamente';
            $response = ['message' => $message];
        } else {
            $response = ['error' => 'No se pudo copiar ningún elemento'];
        }

        if ($errorCount > 0) {
            $response['errors'] = $errorMessages;
        }

        // Agregar registro de resumen a archivo de log
        Log::info("$successCount elementos copiados correctamente y $errorCount elementos fallidos");

        return response()->json($response, $successCount > 0 ? 200 : 500);
    }

    /**
     * Quita la primera barra si es necesario
     */
    private function normalizarRuta($ruta)
    {
        if (strpos($ruta, '/') === 0) {
            $ruta = substr($ruta, 1);
        }

        /* if (strpos($ruta, 'almacen') === 0) {
            $ruta = substr($ruta, 8);
        } */
        return $ruta;
    }



}
