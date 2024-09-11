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
use App\Pigmalion\StorageItem;

// use App\Pigmalion\TiempoEjecucion as T;
//use App\T;
 //use App\Pigmalion\Profiler;

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
    public function list(Request $request, $rutaReq, $json)
    {
        if (strpos($rutaReq, ':') !== false) {
            if ($json) {
                return response()->json(['error' => 'Acceso denegado'], 403);
            }
            abort(403, 'Acceso denegado');
        }

        if (strpos($rutaReq, '..') !== false) {
            if ($json) {
                return response()->json(['error' => 'No se permiten rutas relativas'], 400);
            }
            abort(400, 'No se permiten rutas relativas');
        }

        $dir = new StorageItem($rutaReq);

        $ruta = $dir->relativeLocation;
        $disk = $dir->disk;

        $user = auth()->user();

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

            if ($user)
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
                'ruta' => $dir->location,
                'propietarioRef' => null
            ];
        } else if ($disk == 'archivos' && $ruta == 'mis_archivos') {
            $items = $this->listMyFiles();

            $item = $this->prepareItemRoot();

            array_splice($items, 1, 0, [$item]);

            $respuesta = [
                'items' => $items,
                'ruta' => $dir->location,
                'propietarioRef' => null
            ];
        } else {

            // Comprobar si la carpeta existe
            if (!$dir->exists()) {
                abort(404, 'Ruta no encontrada');
            }

            // si es un archivo, procedemos a la descarga
            if (!$dir->directoryExists()) {
                // no es una carpeta, así que derivamos a la descarga
                return $this->descargar($request, '/' . /* '/archivos/' . */ $ruta);
            }

            // comprobamos el permiso de ejecución (listar) en la carpeta
            $nodo = $this->nodoDesde($dir->location);
            $nodoCarpeta = $nodo;

            $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

            if (!$esAdministrador && Gate::denies('ejecutar', $nodo)) {
                abort(403, 'No tienes permisos para ver la carpeta');
            }

            // creamos la lista de items que retornaremos
            $items = [];

            // elementos de la ruta
            $archivos = $dir->files(true);
            $carpetas = $dir->directories(true);

            // agregamos la carpeta actual
            $items[] = $this->prepareItemList($disk, $ruta, ['tipo' => 'carpeta', 'actual' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);

            // agregamos carpeta padre
            $padre = dirname($ruta);
            if ($padre == '.')
                $padre = "";
            if ($ruta) {
                if ($ruta == 'archivos') {
                    // $items[] = $items[0];
                    if ($user) {
                        $items[] = $this->prepareItemRoot();
                    }
                } else {
                    $nodoPadre = $this->nodoDesde(dirname($dir->location));
                    $items[] = $this->prepareItemList($disk, $padre, ['tipo' => 'carpeta', 'padre' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);
                }
            }

            // Agregar carpetas a la colección de elementos
            foreach ($carpetas as $carpeta) {
                // $dir = StorageItem::build($disk, $carpeta);
                $nodo = null; //$nodosHijos->where('ruta', $carpeta /*$ruta . "/" .  basename($carpeta)*/)->first();
                $items[] = $this->prepareItemList(
                    $disk,
                    $carpeta,
                    [
                        'tipo' => 'carpeta',
                        // 'archivos' => count($dir->files()),
                        // 'subcarpetas' => count($dir->directories())
                    ]
                );
            }

            // Agregar archivos a la colección de elementos
            foreach ($archivos as $archivo) {
                $file = StorageItem::build($disk, $archivo);
                $nodo = null; // $nodosHijos->where('ruta', $archivo)->first();
                $items[] = $this->prepareItemList($disk, $archivo, [
                    'tipo' => 'archivo',
                    'tamano' => $file->size(),
                ]);
            }

            // obtenemos el propietario de la ruta actual, que puede ser un usuario o un grupo/equipo
            $equipo = $nodoCarpeta->group_id ? Equipo::where('group_id', $nodoCarpeta->group_id)->first() : null;
            if ($equipo)
                $propietario = [
                    'url' => route('equipo', $equipo->slug ?? $equipo->id),
                    'nombre' => $equipo->nombre,
                    'tipo' => 'equipo'
                ];
            else {
                $usuario = User::find($nodoCarpeta->user_id);
                $propietario = [
                    'url' => route('usuario', $usuario->slug ?? $usuario->id),
                    'nombre' => $usuario->name,
                    'tipo' => 'usuario'
                ];
            }

            $respuesta = [
                'items' => $items,
                'ruta' => $dir->location,
                // 'rutaBase' => '',
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
     * Obtiene el nodo más cercano siendo el mismo o antecesor de la ubicacion. Agrega la información de propietario
     */
    public static function nodoDesde($ubicacion): ?Nodo
    {
        if ($ubicacion == 'mis_archivos') return null;

        $nodo = Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->whereRaw("'$ubicacion' LIKE CONCAT(nodos.ubicacion, '%')")
            ->orderByRaw('LENGTH(nodos.ubicacion) DESC')
            ->first();

        if (!$nodo) {
            // crea un nodo por con los permisos por defecto
            $nodo = new Nodo();
            $nodo->ubicacion = $ubicacion;
            $nodo->propietario_usuario = "admin"; // valores por defecto
            $nodo->propietario_grupo = "admin";
            // $nodo->save();
        }
        if (!$nodo->propietario_grupo)
            $nodo->propietario_grupo = 'admin';
        if (!$nodo->propietario_usuario)
            $nodo->propietario_usuario = 'admin';
        return $nodo;
    }

    /**
     * Obtiene todos los nodos de un usuario. Agrega la información de propietario (user,grupo)
     */
    private function nodosDeUsuario($idUser)
    {
        return Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.user_id', '=', $idUser)
            ->orderByRaw('LENGTH(nodos.ubicacion) DESC')
            ->get();
    }


    /**
     * Obtiene todos los nodos de la ruta o ubicación, sin incluir el nodo de la carpeta. Agrega la información de propietario
     */
    private function nodosHijos(string $ubicacion)
    {
        return Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.ubicacion', 'LIKE', $ubicacion . '/%')
            ->whereRaw("LENGTH(nodos.ubicacion) - LENGTH(REPLACE(nodos.ubicacion, '/', '')) = " . (substr_count($ubicacion, '/') + 1))
            ->orderByRaw('LENGTH(nodos.ubicacion) ASC')
            ->get();
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

        $nodos = $this->nodosDeUsuario($user->id);

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

            $sti = new StorageItem($nodo->ubicacion);
            if (!$sti->exists()) {
                $nodo->delete();
                continue;
            }

            if ($nodo->es_carpeta)
                $item = $this->prepareItemList($sti->disk, $sti->relativeLocation,  [
                    'tipo' => 'carpeta',
                    'archivos' => count($sti->files()),
                    'subcarpetas' => count($sti->directories()),
                    'acceso_directo' => true,
                ]);
            else
                $item = $this->prepareItemList($sti->disk, $sti->relativeLocation,  [
                    'tipo' => 'archivo',
                    'tamano' => $sti->size(),
                ]);

            $info = $this->prepareItemInfo($nodo->ubicacion, $item['nombre'], $item['tipo'], $nodo);

            $fields = ['nodo_id', 'puedeEscribir', 'puedeLeer', 'permisos', 'propietario', 'privada'];
            foreach ($fields as $field)
                if (isset($info[$field]))
                    $item[$field] = $info[$field];

            $items[] = $item;
        }


        // $ruta = '';

        /* $propietario = [
             'url' => route('usuario', $user->slug ?? $user->id),
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
    private function calcularInfoArchivos($ruta, $items): array
    {
        // comprobamos el permiso de ejecución (listar) en la carpeta
        $nodo = $this->nodoDesde("/" . ltrim($ruta, '/'));
        $nodoCarpeta = $nodo;
        Log::info("calcularInfoArchivos $ruta", ['nodo' => $nodo->toArray(), 'items' => $items]);

        // agregamos carpeta padre
        $padre = dirname($ruta);
        if ($padre == ".")
            $padre = "";
        $nodoPadre = null;
        if ($padre) {

            $nodoPadre = Nodo::desde($padre);
        }

        // obtenemos todos los nodos de la carpeta
        $nodosHijos = $ruta == 'mis_archivos' ? Nodo::whereRaw("false")->get() : $this->nodosHijos($ruta);

        $info = [];

        foreach ($items as $item) {

            $nodo = $item['ruta'] == 'mis_archivos' ? null : $nodosHijos->where('ubicacion', $item['ruta'])->first();
            if (!$nodo)
                $nodo = $this->nodoDesde("/" . $item['ruta']);

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

        Log::info("calcularInfoArchivos.Paso2:", $info);

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
                Log::info("calcularInfoArchivos para item", $item);
                $info[$idx]['puedeEscribir'] = $nodoItem ? Gate::allows('escribir', $nodoItem) : false;
                Log::info("puedeEscribir: " . $info[$idx]['puedeEscribir']);

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
        if($tipo=='carpeta'){
            $dir = new StorageItem($ruta);
            $info_item['archivos'] = count($dir->files());
            $info_item['subcarpetas'] = count($dir->directories());
        }
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

        $info = $this->calcularInfoArchivos($ruta, $items);

        return response()->json($info, 200);
    }

    private function prepareItemRoot()
    {
        return [
            'nombre' => 'raiz',
            'ruta' => '',
            'carpeta' => '',
            'tipo' => 'carpeta',
            'actual' => true,
            'tamano' => 0,
            'archivos' => 0,
            'subcarpetas' => 2,
            'url' => '/archivos_raiz'
        ];
    }

    /**
     * Prepara el item de un listado de una carpeta
     */
    private function prepareItemList(string $disk, string $ruta, array $options): array
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
        $ruta = $request->ruta;

        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        $dir = new StorageItem($ruta);

        // nombre a buscar
        $nombre = $request->nombre;

        // ruta donde empezar a buscar
        // $baseUrl = url('');
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

            // la ruta inicial es la primera carpeta donde buscaremos después en disco
            $carpetas_pendientes = [$dir->location];

            Log::info("Nueva busqueda en $ruta. Buscando $nombre ...");
            Log::info("carpetas_pendientes", $carpetas_pendientes);

            // realizamos una rápida búsqueda inicial, usando nodos
            $nodos = Nodo::search($nombre)->query(function ($query) use ($dir) {
                return $query->whereRaw("ubicacion LIKE '{$dir->location}%'");
            })->take(50)->get();

            $resultados = [];

            foreach ($nodos as $nodo) {
                $sti = new StorageItem($nodo->ubicacion);

                Log::info("nodo encontrado: " . $nodo->id . " - " . $nodo->nombre . " - " . $nodo->ubicacion);
                if (!$sti->exists()) {
                    Log::info("no existe archivo o carpeta");
                    continue;
                }

                $acceso = $esAdministrador;

                if (!$acceso) {
                    // comprovar visibilidad, miramos la carpeta padre
                    $nodoPadre = Nodo::desde(dirname($nodo->ubicacion));
                    $acceso = $nodo && Gate::allows('ejecutar', $nodoPadre);
                }

                if (!$acceso) {
                    continue;
                }

                if ($nodo->es_carpeta)
                    $resultados[] = $this->prepareItemList($sti->disk, $sti->relativeLocation, [
                        'tipo' => 'carpeta',
                        'archivos' => count($sti->files()),
                        'subcarpetas' => count($sti->directories())
                    ]);
                else
                    $resultados[] = $this->prepareItemList($sti->disk, $sti->relativeLocation, [
                        'tipo' => 'archivo',
                        'tamano' => $sti->size(),
                    ]);
            }

            // guardamos las carpetas pendientes en la sesión:
            $request->session()->put('carpetas_pendientes', $carpetas_pendientes);

            Log::info("guardamos carpetas_pendientes", $carpetas_pendientes);

            // generamos un id de busqueda
            $id_busqueda_actual = uniqid();

            $request->session()->put('id_busqueda', $id_busqueda_actual);
            $request->session()->put('buscar_ruta', $dir->location);
            $request->session()->put('buscar_nombre', $nombre);
        } else {

            // continuación de búsqueda, ahora en el sistema de archivos
            Log::info("continuacion de busqueda");

            // recuperamos los parámetros de búsqueda de la sesión
            $carpetas_pendientes = $request->session()->get('carpetas_pendientes');
            $nombre = $request->session()->get('buscar_nombre');
            $ruta = $request->session()->get('buscar_ruta');

            Log::info("recuperamos nombre: $nombre, ruta: $ruta");
            Log::info("recuperamos carpetas_pendientes", $carpetas_pendientes);

            // realizamos una busqueda real en disco

            $start_time = microtime(true);
            $tiempo_transcurrido = 0;
            $resultados = [];

            // $baseUrl = url('');

            // tiempo máximo de búsqueda: 900 milisegundos
            while (count($carpetas_pendientes) > 0 && $tiempo_transcurrido < 900) {
                // Obtener la primera carpeta pendiente para procesar
                $carpeta = array_shift($carpetas_pendientes);
                if (!$carpeta)
                    continue;
                // dd($carpeta);
                //if (!preg_match("/^archivos/", $carpeta))
                //  continue;

                // Buscar archivos y subcarpetas dentro de la carpeta actual
                Log::info("Buscando en carpeta $carpeta");
                $dir = new StorageItem($carpeta);
                $archivos = $dir->files(true);
                $subcarpetas = $dir->directories(true);

                // Comprobar si algún archivo tiene un nombre similar a la cadena de búsqueda
                foreach ($archivos as $archivo) {
                    Log::info("archivo $archivo");
                    if ($this->matchSearch(basename($archivo), $nombre))
                        $resultados[] = $this->prepareItemList($dir->disk, $archivo, [
                            'tipo' => 'archivo',
                            'tamano' => Storage::disk($dir->disk)->size($archivo)
                        ]);
                }

                // Comprobar si alguna subcarpeta tiene un nombre similar a la cadena de búsqueda
                foreach ($subcarpetas as $subcarpeta) {
                    $item = null;
                    $dir = StorageItem::build($dir->disk, $subcarpeta);
                    Log::info("carpeta $subcarpeta");

                    if ($this->matchSearch(basename($subcarpeta), $nombre)) {
                        $item = $this->prepareItemList($dir->disk, $subcarpeta, [
                            'tipo' => 'carpeta',
                            'archivos' => count($dir->files()),
                            'subcarpetas' => count($dir->directories())
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
        return levenshtein($term, $str, 1, 3, 4) < 5;
    }



    /**
     * Acceso público al almacen de medios
     */
    public function almacen(string $ruta)
    {
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        $sti = new StorageItem($ruta);

        if ($sti->disk != 'public')
            abort(403, 'No tienes permisos.');

        if ($sti->disk == 'public' && $sti->directoryExists())
            abort(403, 'Acceso no permitido.');

        $path = $sti->path;
        $mime = $sti->mimeType();

        return response()->file($path, ['Content-Type' => $mime]);
    }

    /**
     * Controla el acceso a las descargas
     */
    public function descargar(Request $request, string $ruta)
    {
        // Log::info("Descargar $ruta");

        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        $sti = new StorageItem($ruta);

        // no se puede descargar una carpeta
        if ($sti->directoryExists())
            abort(403, 'Acceso no permitido.');

        if (!$sti->exists())
            abort(404);

        $user = auth()->user();
        $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

        if ($sti->disk != 'public') {
            // obtenemos el nodo correspondiente
            $nodo = Nodo::desde($ruta);

            // comprobamos permisos de lectura
            if (!$esAdministrador && Gate::denies('leer', $nodo))
                abort(403, 'No tienes permisos.');
        }

        $mime = $sti->mimeType();
        $path = $sti->path;

        // si es una imagen
        if ($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif') {
            $controller = new ImagenesController();
            return $controller->descargar($request, $ruta);
        }


        return response()->file($path, ['Content-Type' => $mime]);
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

        $dir = new StorageItem($folder);

        $folder = $dir->relativeLocation;

        $path = $dir->path;
        // dd($path);
        Log::info("uploading file '$path', original location: $ofolder, in disk {$dir->disk}, relative folder: $folder");

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
        $storedPath = $file->storeAs($folder, $filename, $dir->disk);

        Log::info("storedPath: $storedPath");

        // creamos su nodo
        Nodo::crear($dir->location . '/' . $filename, false, auth()->user());

        // Obtener la URL pública del archivo
        //$baseUrl = Storage::disk($disk)->url('');
        $upItem = StorageItem::build($dir->disk, $storedPath);
        $url = $upItem->url;
        // Log::info("baseUrl = $baseUrl");
        // removemos el protocolo y host de la url
        $url = preg_replace('#^https?://[^/]+#', '', $url);
        //$url = str_replace($baseUrl, '', $url);

        Log::info("FIle uploaded: $url");

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

        return $this->processUpload($request, $file, $request->destinationPath);
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

        // detecta si estamos editando un tipo de datos y lo extrae, para asignarle después una carpeta
        /* $url = $request->headers->get('referer');
    if ($url && preg_match('/admin\/(.*?)\/\d+\/edit/', $url, $matches)) {
        $folder = $matches[1];
    } else {
        // $folder = null;
    } */
        return $this->processUpload($request, $file, $request->destinationPath);
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

        $folder = $request->folder;
        $name = $request->name;

        if (!$folder || $folder == "/") {
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

        $dir = new StorageItem($folder);

        if ($dir->disk == 'raiz') {
            return response()->json([
                'error' => 'notAllowed'
            ], 403);
        }

        // $folderPath = Storage::disk($disk)->path($folder);

        if (!$dir->directoryExists()) {
            return response()->json([
                'error' => 'folderNotFound'
            ], 404);
        }

        $newFolderPath = $dir->path . '/' . $name;

        Log::info("newFolderPath: $newFolderPath");

        if (file_exists($newFolderPath)) {
            return response()->json([
                'error' => 'folderAlreadyExists'
            ], 409);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // comprobamos los permisos de escritura
        //$acl = Acl::from($user);
        if (!$esAdministrador) {
            $nodo = Nodo::desde($dir->location);
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
        Nodo::crear($dir->location . '/' . $name, true, $user);

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

        // Verificar si la ruta contiene saltos de carpeta
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'Ruta relativa no permitida'], 400);
        }

        $sti = new StorageItem($ruta);

        if ($sti->isSpecial()) {
            return response()->json(['error' => 'No permitido'], 403);
        }

        // Verificar que el archivo exista
        if (!$sti->exists()) {
            return response()->json(['error' => 'El archivo no existe'], 404);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        $nodoItem = Nodo::desde($sti->location);

        // comprobamos los permisos de escritura
        if (!$esAdministrador) {
            if (!$nodoItem || Gate::denies('escribir', $nodoItem)) {
                return response()->json([
                    'error' => 'No tienes permisos'
                ], 403);
            }
        }

        // comprobamos sticky bit y acl
        $nodoContenedor = Nodo::desde(dirname($sti->location));
        if ($nodoContenedor->sticky && $nodoItem->user_id != $user->id) {
            if (!$nodoItem->tieneAcceso($user, 'escribir'))
                return response()->json([
                    'error' => 'No tienes permisos de propietario'
                ], 403);
        }



        // Verificar si la ruta es una carpeta
        if ($sti->directoryExists()) {
            // Verificar si la carpeta está vacía antes de eliminarla
            if (count($sti->allFiles()) > 0) {
                return response()->json(['error' => 'No se puede eliminar la carpeta porque no está vacía'], 400);
            }

            // Eliminar la carpeta vacía
            if ($sti->deleteDirectory()) {
                return response()->json(['message' => 'Carpeta eliminada correctamente'], 200);
            } else {
                return response()->json(['error' => 'No se pudo eliminar la carpeta'], 500);
            }
        }

        // Intentar eliminar el archivo
        else if ($sti->delete()) {
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

        $sti = new StorageItem($ruta);

        if (!$sti->exists()) {
            return response()->json(['error' => "La ruta '$ruta' no existe"], 404);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        $nodoItem = Nodo::desde($sti->location);

        // se requiere permisos de escritura en el nodo
        if (!$esAdministrador) {
            if (!$nodoItem || $nodoItem->user_id != $user->id) {
                // El usuario tiene el permiso "administrar archivos/*Gate::denies('escribir', $nodoItem)*/) {
                return response()->json([
                    'error' => 'No tienes permisos'
                ], 403);
            }
        }

        // comprobamos sticky bit y acl
        $nodoContenedor = Nodo::desde(dirname($sti->location));
        if ($nodoContenedor->sticky && $nodoItem->user_id != $user->id) {
            if (!$nodoItem->tieneAcceso($user, 'escribir'))
                return response()->json([
                    'error' => 'No tienes permisos de propietario'
                ], 403);
        }

        $esCarpeta = $sti->directoryExists();

        $update = [];
        if ($permisos)
            $update['permisos'] = $permisos;
        if ($request->user_id)
            $update['user_id'] = $request->user_id;
        if ($request->group_id)
            $update['group_id'] = $request->group_id;

        // queremos un nodo de esta ruta, no heredado
        $nodo = Nodo::where('ubicacion', $sti->location)->first();
        if (!$nodo) {
            $nodo = Nodo::create([
                'ubicacion' => $sti->location,
                'es_carpeta' => $esCarpeta,
                'user_id' => $update['user_id'] ? $update['user_id'] : $nodoItem->user_id,
                'group_id' => $update['group_id'] ? $update['group_id'] : $nodoItem->group_id,
                'permisos' => $update['permisos'] ? $update['permisos'] : $nodoItem->permisos,
            ]);
        } else {
            if (count($update))
                $nodo->update($update);
        }

        $item = $this->prepareItemList($sti->disk, $sti->relativeLocation, ['tipo' => $sti->directoryExists() ? 'carpeta' : 'archivo']);

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


        $info = $this->calcularInfoArchivos($ruta, [$item]);
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

        $ruta = $request->folder;

        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        $dir = new StorageItem($request->folder);


        //  dd("folder=$folder  oldName=$oldName  newName=$newName");

        // Verificar si faltan parámetros
        if (!$request->filled(['folder', 'oldName', 'newName'])) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        $oldName = $request->oldName;
        $newName = $request->newName;

        //$ruta = $dir->relativeLocation;
        $rutaAntes = $dir->location . '/' . $oldName;
        $rutaDespues = $dir->location . '/' . $newName;
        $itemAntes = new StorageItem($rutaAntes);
        $itemDespues = new StorageItem($rutaDespues);

        // dd("itemAntes=$itemAntes itemDespues=$itemDespues");

        // Verificar que el item exista
        if (!$itemAntes->exists()) {
            return response()->json(['error' => "El elemento '$rutaAntes' no existe"], 404);
        }

        if ($itemDespues->exists()) {
            return response()->json(['error' => "Ya existe '$rutaDespues'"], 400);
        }

        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        if (!$esAdministrador) {
            // se requiere: permisos de escritura en la carpeta contenedora del item, y permisos de escritura en el item
            $nodoContenedor = Nodo::desde($dir->location);
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


        $rutaAbsolutaAntes = realpath($itemAntes->path);
        $rutaAbsolutaDespues = $itemDespues->path;

        //Log::info("Laravel move($itemAntes, $itemDespues) disk=$disk");
        Log::info("rename($rutaAbsolutaAntes, $rutaAbsolutaDespues) disk={$itemAntes->disk}");
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
        $items = $request->items;

        if (!$sourceFolder || !$destinationFolder) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        if (strpos($sourceFolder, '..') !== false || strpos($destinationFolder, '..') !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }



        /*if ($disk1 != $disk2) {
            return response()->json(['error' => 'No se permite mover entre discos'], 403);
        }*/

        // $disk = $disk1;

        if (!is_array($items)) {
            return response()->json(['error' => 'Los elementos no son un array'], 400);
        }

        $source = new StorageItem($sourceFolder);
        $destination = new StorageItem($destinationFolder);

        if ($source->isSpecial() || $destination->isSpecial()) {
            return response()->json(['error' => 'No permitido'], 403);
        }

        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        // dd($nodoSource);

        if (!$esAdministrador) {
            $nodoSource = Nodo::desde($source->location);
            $nodoDestination = Nodo::desde($destination->location);

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
            $rutaAntes = $source->location . "/" . $item;
            $rutaDespues = $destination->location . "/" . $item;
            $itemSource = new StorageItem($rutaAntes);
            $itemDestination = new StorageItem($rutaDespues);

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
            if (!$itemSource->exists()) {
                $errorCount++;
                $errorMessages[] = "El item '$item' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if ($itemSource->directoryExists()) {
                // Intentar mover la carpeta
                if (File::moveDirectory($itemSource->path, $itemDestination->path)) {
                    // if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Carpeta '$item' movida de '{$source->location}' a '{$destination->location}'");

                    // cambias los nodos afectados
                    Nodo::mover($rutaAntes, $rutaDespues);
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo mover la carpeta '{$itemSource->location}'";
                }
            } else {
                // Verificar si el archivo de destino ya existe
                $counter = 1;
                while ($itemDestination->exists()) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $rutaDespues = $destination->location . "/" . $itemBaseName . '.' . $itemExtension;
                    $itemDestination = new StorageItem($rutaDespues);
                    $counter++;
                }

                if (File::move($itemSource->path, $itemDestination->path)) {
                    // if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Archivo '$item' movido de '{$source->location}' a '{$destination->location}'");

                    // debemos renombrar todas las rutas afectadas en los nodos
                    Nodo::mover($rutaAntes, $rutaDespues);
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo mover el archivo '{$itemSource->location}'";
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

        $items = $request->items;

        if (!is_array($items)) {
            return response()->json(['error' => 'Los elementos no son un array'], 400);
        }

        $source = new StorageItem($sourceFolder);
        $destination = new StorageItem($destinationFolder);

        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $esAdministrador = $user->hasPermissionTo('administrar archivos');

        if (!$esAdministrador) {
            $nodoSource = Nodo::desde($source->location);
            $nodoDestination = Nodo::desde($destination->location);
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
            $rutaAntes = $source->location . "/" . $item;
            $rutaDespues = $destination->location . "/" . $item;
            $itemSource = new StorageItem($rutaAntes);
            $itemDestination = new StorageItem($rutaDespues);

            // Verificar que el item exista
            if (!$itemSource->exists()) {
                $errorCount++;
                $errorMessages[] = "El elemento '{$itemSource->location}' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if ($itemSource->directoryExists()) {
                // Intentar copiar la carpeta
                // if($diskSource == $diskDest)
                //   $result =  Storage::disk($disk)->copyDirectory($itemSource, $itemDestination);
                //else {
                $result = File::copyDirectory($itemSource->path, $itemDestination->path);
                //}
                if ($result) {
                    $successCount++;
                    // Agregar registro de copia a archivo de log
                    Log::info("Carpeta '$item' copiada de '$sourceFolder' a '$destinationFolder'");
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar la carpeta '{$itemSource->path}' a '{$itemDestination->path}'";
                }
            } else {
                // Verificar si el archivo de destino ya existe, en tal caso le añadiremos un sufijo _n
                $counter = 1;
                while ($itemDestination->exists()) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $rutaDespues = $destination->location . "/" . $itemBaseName . '.' . $itemExtension;
                    $itemDestination = new StorageItem($rutaDespues);
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

                $result = File::copy($itemSource->path, $itemDestination->path);

                if ($result) {
                    $successCount++;
                    Log::info("Archivo '$item' copiado de '$sourceFolder' a '$destinationFolder'");
                } else {
                    // Agregar registro de copia a archivo de log
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar el archivo '{$itemSource->path}' a '{$itemDestination->path}'";
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
}


class NodoPropietario
{
}
