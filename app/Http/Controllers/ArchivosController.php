<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Nodo;
use App\Models\Acl;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;


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
        return $this->list($request->path(), false);
    }


    /**
     * Para el gestor de archivos o Media Manager
     */
    public function filemanager(Request $request, $ruta = "/")
    {
        return $this->list($ruta, true);
    }

    /**
     * Listado de una carpeta. Comprueba todos los permisos de acceso
     */
    public function list($ruta, $json)
    {
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

        // Obtener la URL relativa actual de la aplicación
        $baseUrl = url('');

        $rutaBase = str_replace($baseUrl, '', str_replace('/storage', '', Storage::disk('public')->url($ruta)));


        // Comprobar si la carpeta existe
        if (!Storage::disk('public')->exists($rutaBase)) {
            abort(404, 'Ruta no encontrada');
        }
        if (!Storage::disk('public')->directoryExists($rutaBase)) {
            // no es una carpeta, así que derivamos a la descarga
            return $this->storage($rutaBase);
        }

        // $user = auth()->user();

        // $acl = Acl::from($user, ['ejecutar', 'escribir']);

        // comprobamos el permiso de ejecución (listar) en la carpeta
        $nodo = Nodo::desde($ruta);
        $nodoCarpeta = $nodo;

        if (Gate::denies('ejecutar', $nodo)) {
            throw new AuthorizationException('No tienes permisos para ver la carpeta', 403);
        }



        $items = [];

        // elementos de la ruta
        $archivos = Storage::disk('public')->files($ruta);
        $carpetas = Storage::disk('public')->directories($ruta);

        // agregamos la carpeta actual
        $items[] = $this->prepareItemList($ruta, $nodo, ['tipo' => 'carpeta', 'actual' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);

        // agregamos carpeta padre
        $padre = dirname($ruta);
        $nodoPadre = null;
        if ($padre && $padre != "\\" && $padre != "//") {
            $nodoPadre =  Nodo::desde($padre);
            $items[] = $this->prepareItemList($padre, $nodoPadre, ['tipo' => 'carpeta', 'padre' => true, 'archivos' => count($archivos), 'subcarpetas' => count($carpetas)]);
        }


        // obtenemos todos los nodos de la carpeta
        $nodosHijos = Nodo::hijos($ruta);

         // Obtenemos todos los ids de todos los nodos implicados
         $nodosIdsArr =$nodosHijos->pluck('id')->toArray();
        if($nodoPadre)
            array_push($nodosIdsArr, $nodoPadre->id);
        if($nodo)
            array_push($nodosIdsArr, $nodo->id);

        // Agregar carpetas a la colección de elementos
        foreach ($carpetas as $carpeta) {
            $nodo = $nodosHijos->where('ruta', $carpeta /*$ruta . "/" .  basename($carpeta)*/)->first();
            $items[] = $this->prepareItemList(
                $carpeta,
                $nodo,
                [
                    'tipo' => 'carpeta',
                    'archivos' => count(Storage::disk('public')->files($carpeta)),
                    'subcarpetas' => count(Storage::disk('public')->directories($carpeta))
                ]
            );
        }

        // Agregar archivos a la colección de elementos
        foreach ($archivos as $archivo) {
            $nodo = $nodosHijos->where('ruta', $archivo)->first();
            $items[] = $this->prepareItemList($archivo, $nodo, [
                'tipo' => 'archivo',
                'tamano' => Storage::disk('public')->size($archivo),
            ]);
        }

        // comprobamos los permisos de escritura (para saber si puede crear carpetas, o renombrar archivos)
        $puedeEscribir = Gate::allows('escribir', $nodoCarpeta);

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

        // Obtenemos de una vez todos los ACL
        $acl = Acl::inNodes($nodosIdsArr);

        // Agregamos la información de Access Control List para cada item
         foreach($items as $idx=>$item) {
            if($item['nodo_id']) {
                $a = $acl->where('nodo.id', '=', $item['nodo_id'])->toArray();
                foreach($a as $k=>$x) {
                    unset($a[$k]['nodo']);
                    unset($a[$k]['nodo_id']);
                }
                $items[$idx]['acl'] = array_values($a);
            }
            else
                $items[$idx]['acl'] = null;
         }

        $respuesta = [
            'items' => $items,
            'ruta' => $ruta,
            'puedeEscribir' => $puedeEscribir,
            'propietario' => $propietario
        ];

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
     * Prepara el item de un listado de una carpeta
     */
    private function prepareItemList(string $ruta, ?Nodo $nodo,array $options): array
    {
        $baseUrl = url('');
        $rutaBase = str_replace($baseUrl, '', str_replace('/storage', '', Storage::disk('public')->url($ruta)));
        $rutaPadre = str_replace("\\", "/", dirname($rutaBase));
        $item = [
            'nombre' => basename($ruta),
            'ruta' => $ruta,
            // 'url' => ($options['tipo'] ?? '') == 'archivo' ? '/storage' . $rutaBase : $rutaBase,
            'url' => str_replace(' ', '%20', $rutaBase),
            'carpeta' => $rutaPadre,
            'fecha_modificacion' => Storage::disk('public')->lastModified($ruta),
        ];
        $item = array_merge($item, $options);
        if (!$nodo)
            $nodo = Nodo::desde($ruta);
        if (!$nodo || (($options['tipo'] ?? '') == 'carpeta' && Gate::denies('ejecutar', $nodo)))
            $item['privada'] = true; // carpeta no accesible
        $item['nodo_id'] = optional($nodo)->id ?? null;
        $item['permisos'] = optional($nodo)->permisos ?? 0;
        $item['propietario'] = ['usuario' => optional($nodo)->propietario_usuario, 'grupo' => optional($nodo)->propietario_grupo];
        return $item;
    }

    /**
     * Esta función ajax sirve para buscar progresivamente archivos que tengan o contengan un nombre
     */
    public function buscar(Request $request)
    {
        // nombre a buscar
        $nombre = $request->nombre;

        // ruta donde empezar a buscar
        $baseUrl = url('');
        $ruta = ltrim(str_replace($baseUrl, '', $request->ruta), "/");

        // carpetas donde falta buscar
        $carpetas_pendientes = @json_decode($request->carpetas_pendientes, false);

        // estamos en la primera busqueda (porque no hay carpetas pendientes)
        if (empty($carpetas_pendientes)) {

            // la ruta inicial es la primera carpeta donde buscaremos después en disco
            $carpetas_pendientes = [$ruta];

            // realizamos una rápida búsqueda inicial, usando nodos

            $nodos = Nodo::search($nombre)->query(function ($query) use ($ruta) {
                return $query->whereRaw("ruta LIKE '$ruta%'");
            })->take(50)->get();

            $resultados = [];
            foreach ($nodos as $nodo) {
                if (!Storage::disk('public')->exists($nodo->ruta))
                    continue;
                if ($nodo->es_carpeta)
                    $resultados[] = $this->prepareItemList($nodo->ruta, $nodo, [
                        'tipo' => 'carpeta',
                        'archivos' => count(Storage::disk('public')->files($nodo->ruta)),
                        'subcarpetas' => count(Storage::disk('public')->directories($nodo->ruta))
                    ]);
                else
                    $resultados[] = $this->prepareItemList($nodo->ruta, $nodo, [
                        'tipo' => 'archivo',
                        'tamano' => Storage::disk('public')->size($nodo->ruta),
                    ]);
            }

            $carpetas_pendientes = [$ruta];
            $carpetas_pendientes = [$ruta];

        } else {

            // realizamos una busqueda real en disco

            $start_time = microtime(true);
            $tiempo_transcurrido = 0;
            $resultados = [];

            // tiempo máximo de búsqueda: 500 milisegundos
            while (count($carpetas_pendientes) > 0 && $tiempo_transcurrido < 500) {
                // Obtener la primera carpeta pendiente para procesar
                $carpeta = array_shift($carpetas_pendientes);
                if (!$carpeta)
                    continue;
                $carpeta = ltrim(str_replace($baseUrl, '', $carpeta), "/");
                if (!preg_match("/^archivos/", $carpeta))
                    continue;

                // Buscar archivos y subcarpetas dentro de la carpeta actual
                $archivos = Storage::disk('public')->files($carpeta);
                $subcarpetas = Storage::disk('public')->directories($carpeta);

                // Comprobar si algún archivo tiene un nombre similar a la cadena de búsqueda
                foreach ($archivos as $archivo) {
                    if ($this->matchSearch(basename($archivo), $nombre))
                        $resultados[] = $this->prepareItemList($archivo, null, [
                            'tipo' => 'archivo',
                            'tamano' => Storage::disk('public')->size($archivo)
                        ]);
                }

                // Comprobar si alguna subcarpeta tiene un nombre similar a la cadena de búsqueda
                foreach ($subcarpetas as $subcarpeta) {
                    $item = null;
                    if ($this->matchSearch(basename($archivo), $nombre)) {
                        $item = $this->prepareItemList($archivo, null, [
                            'tipo' => 'carpeta',
                            'archivos' => count(Storage::disk('public')->files($carpeta)),
                            'subcarpetas' => count(Storage::disk('public')->directories($carpeta))
                        ]);
                        $resultados[] = $item;
                    }

                    // miramos si podemos buscar en esta carpeta
                    if ($item == null) {
                        $nodo = Nodo::desde($ruta);
                        if (!$nodo || Gate::denies('ejecutar', $nodo))
                            $item['privada'] = true; // carpeta no accesible
                    }

                    if (!($item['privada'] ?? false))
                        // para que se procese en la siguiente iteración del bucle
                        $carpetas_pendientes[] = $subcarpeta;

                }

                // Calcular el tiempo transcurrido en milisegundos
                $tiempo_transcurrido = (microtime(true) - $start_time) * 1000;
            }
        }

        $response = [
            'resultados' => $resultados,
            'carpetas_pendientes' => $carpetas_pendientes
        ];

        return response()->json($response, 200);

    }

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
     * Controla el acceso a las descargas
     */
    public function storage(string $ruta)
    {
        $path = storage_path('app/public/' . $ruta);

        /*if (Gate::denies('download-file', $path)) {
            abort(403, 'Unauthorized action.');
        }*/

        return response()->download($path);
    }




    ////////////////////////////////////////////////////////////////////
    ///// API
    ////////////////////////////////////////////////////////////////////


    /**
     * Procesa la subida de archivos
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
                'error' => 'typeNotAllowed'
            ], 415);
        }

        if ($file->getSize() > 5000000) {
            return response()->json([
                'error' => 'fileTooLarge'
            ], 413);
        }

        $path = Storage::disk('public')->path($folder);
        //dd($path);

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
        $folderPath = storage_path("app/public/" . $folder);

        while (File::exists($folderPath . '/' . $newFilename)) {
            $newFilename = $baseFilename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $filename = $newFilename;
        $storedPath = $file->storeAs($folder, $filename, 'public');

        // creamos su nodo
        Nodo::crear($folder . '/' . $filename, false, auth()->user());

        // Obtener la URL pública del archivo
        $url = Storage::url($storedPath);

        return response()->json([
            'data' => [
                'filePath' => "/" . substr($url, 1)
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

        $folderPath = storage_path("app/public/" . $folder);

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

        // comprobamos los permisos de escritura
        //$acl = Acl::from($user);
        $nodo = Nodo::desde($folder);
        if (!$nodo || Gate::denies('escribir', $nodo)) {
            return response()->json([
                'error' => 'No tienes permisos'
            ], 403);
        }

        // creamos la carpeta
        if (!mkdir($newFolderPath, 0755)) {
            return response()->json([
                'error' => 'unableToCreateFolder'
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
     */
    public function delete($ruta)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        // Concatenar la ruta completa al archivo
        $archivo = 'public' . str_replace('/storage', '', $ruta);

        // Verificar si la ruta contiene saltos de carpeta
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        // Verificar que el archivo exista
        if (!Storage::exists($archivo)) {
            return response()->json(['error' => 'El archivo no existe'], 404);
        }

        // comprobamos los permisos de escritura
        //$acl = Acl::from($user);
        $nodo = Nodo::desde($ruta);
        if (!$nodo || Gate::denies('escribir', $nodo)) {
            return response()->json([
                'error' => 'No tienes permisos'
            ], 403);
        }

        // Verificar si la ruta es una carpeta
        if (Storage::directoryExists($archivo)) {
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
        else if (Storage::delete($archivo)) {
            return response()->json(['message' => 'Archivo eliminado correctamente'], 200);
        } else {
            return response()->json(['error' => 'No se pudo eliminar el archivo'], 500);
        }
    }



    /**
     * Cambia los permisos de un nodo
     * parámetros: ruta, user_id, group_id, permisos
     */
    public function update(Request $request) {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $ruta = $this->normalizarRuta($request->ruta);
        $permisos = $request->permisos;

        if (!Storage::exists($ruta)) {
            return response()->json(['error' => "La ruta '$ruta' no existe"], 404);
        }


        // se requiere permisos de escritura en el nodo
        $nodoItem = Nodo::desde($ruta);
        if (!$nodoItem || Gate::denies('escribir', $nodoItem)) {
            return response()->json([
                'error' => 'No tienes permisos'
            ], 403);
        }

        // comprobamos sticky bit. Si está activado no podemos modificar un archivo o carpeta si no es de nuestra propiedad
        $nodoContenedor = Nodo::desde(dirname($ruta));
        if($nodoContenedor->sticky && $nodoItem->user_id!=$user->id) {
            return response()->json([
                'error' => 'No tienes permisos de propietario'
            ], 403);
        }

        $esCarpeta = Storage::directoryExists($ruta);

        $update = [];
        if($permisos)
            $update['permisos'] = $permisos;
        if($request->user_id)
            $update['user_id'] = $request->user_id;
        if($request->group_id)
            $update['group_id'] = $request->group_id;

        $nodo = Nodo::where('ruta', $ruta)->first();
        if(!$nodo) {
            Nodo::create([
                'ruta'=>$ruta,
                'es_carpeta'=>$esCarpeta,
                'user_id' =>$update['user_id']?$update['user_id']:$nodoItem->user_id,
                'group_id' =>$update['group_id']?$update['group_id']:$nodoItem->group_id,
                'permisos' => $update['permisos']?$update['permisos']:$permisos,
            ]);
        }else {
            $nodo->update($update);
        }

        return response()->json(['message' => $esCarpeta?"Carpeta modificada":"Archivo modificado"], 200);
    }

    /**
     * Renombra un archivo o carpeta que está en una carpeta $folder de viejo nombre $oldName a $newName
     * parámetros: folder (carpeta donde está el nodo), oldName (nombre actual del nodo), newName (nuevo nombre del nodo)
     */
    public function rename(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $folder = $this->normalizarRuta($request->folder);
        $oldName = $request->oldName;
        $newName = $request->newName;
        //  dd("folder=$folder  oldName=$oldName  newName=$newName");

        // Verificar si faltan parámetros
        if (!$request->filled(['folder', 'oldName', 'newName'])) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        if ($folder == ".." || strpos($folder, "../") !== false || strpos($folder, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        $rutaAntes = $folder . '/' . $oldName;
        $rutaDespues = $folder . '/' . $newName;
        $itemAntes = 'public' . '/' . $rutaAntes;
        $itemDespues = 'public' . '/' . $rutaDespues;

        // dd("itemAntes=$itemAntes itemDespues=$itemDespues");

        // Verificar que el item exista
        if (!Storage::exists($itemAntes)) {
            return response()->json(['error' => "El item '$itemAntes' no existe"], 404);
        }


        // se requiere: permisos de escritura en la carpeta contenedora del item, y permisos de escritura en el item
        $nodoContenedor = Nodo::desde($folder);
        $nodoItem = Nodo::desde($rutaAntes);
        if (!$nodoContenedor || !$nodoItem || Gate::denies('escribir', $nodoContenedor) || Gate::denies('escribir', $nodoItem)) {
            return response()->json([
                'error' => 'No tienes permisos de escritura'
            ], 403);
        }

        /*
        En sistemas operativos basados en UNIX, como Linux, el sticky bit es un atributo de permisos especial que se puede aplicar a directorios. Este bit tiene un propósito específico y afecta la forma en que los usuarios pueden acceder y manipular archivos dentro de ese directorio.
        Cuando el sticky bit está configurado en un directorio, los usuarios pueden eliminar o renombrar únicamente los archivos que son de su propiedad. Esto significa que, incluso si otros usuarios tienen permisos de escritura en el directorio, no podrán eliminar o renombrar archivos que no sean de su propiedad.
        El propósito principal del sticky bit es asegurar que los usuarios no eliminen o modifiquen archivos de otros usuarios en directorios compartidos, como /tmp. Este directorio suele tener el sticky bit activado para evitar que los usuarios borren o modifiquen archivos de otros usuarios en un entorno multiusuario.
        */
        if($nodoContenedor->sticky && $nodoItem->user_id!=$user->id) {
            return response()->json([
                'error' => 'No tienes permisos de propietario'
            ], 403);
        }

        //$rutaAbsolutaAntes = realpath(Storage::disk('public')->path($rutaAntes));
        //$rutaAbsolutaDespues = preg_replace("/[\/\\\\]/", DIRECTORY_SEPARATOR, Storage::disk('public')->path($rutaDespues));

        // dd("rename($rutaAbsolutaAntes, $rutaAbsolutaDespues");
        // Verificar si el item es una carpeta
        if (Storage::directoryExists($itemAntes)) {
            // Intentar renombrar la carpeta
            if (Storage::move($itemAntes, $itemDespues)) {
                //if (rename($rutaAbsolutaAntes, $rutaAbsolutaDespues)) {
                $response = response()->json(['message' => 'Carpeta renombrada correctamente'], 200);
            } else {
                //$error = error_get_last();
                //dd($error);
                return response()->json(['error' => 'No se pudo renombrar la carpeta'], 500);
            }
        } else {
            // Intentar renombrar el archivo
            if (Storage::move($itemAntes, $itemDespues)) {
                //if (rename($rutaAbsolutaAntes, $rutaAbsolutaDespues)) {
                // actualizamos la ruta del nodo
                $response = response()->json(['message' => 'Archivo renombrado correctamente'], 200);
            } else {
                //$error = error_get_last();
                //dd($error);
                return response()->json(['error' => 'No se pudo renombrar el archivo'], 500);
            }
        }

        // debemos renombrar todas las rutas afectadas en los nodos
        Nodo::mover($rutaAntes, $rutaDespues);

        return $response;
    }


    /**
     * Mueve un conjunto de archivos a otra carpeta
     * sourceFolder, destinationFolder, items
     */
    public function move(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $sourceFolder = $this->normalizarRuta($request->sourceFolder);
        $destinationFolder = $this->normalizarRuta($request->targetFolder);

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

        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $nodoSource = Nodo::desde($sourceFolder);
        $nodoDestination = Nodo::desde($destinationFolder);

        // dd($nodoSource);

        if (!$nodoSource || Gate::denies('leer', $nodoSource)) {
            return response()->json([
                'error' => 'No tienes permisos de lectura en la carpeta origen'
            ], 403);
        }
       if (Gate::denies('escribir', $nodoSource)) {
            return response()->json([
                'error' => 'No tienes permisos de escritura en la carpeta origen'
            ], 403);
        }
        if (!$nodoDestination || Gate::denies('escribir', $nodoDestination)) {
            return response()->json([
                'error' => 'No tienes permisos en la carpeta destino'
            ], 403);
        }


        // Mover cada item a la nueva carpeta de destino
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($items as $item) {
            $rutaAntes = $sourceFolder . "/" . $item;
            $rutaDespues = $destinationFolder . "/" . $item;
            $itemSource = 'public/' . $rutaAntes;
            $itemDestination = 'public/' . $rutaDespues;

            // Comprobamos sticky bit (si está activado no podemos mover archivos o carpetas que no son nuestros)
            if($nodoSource->sticky) {
                $nodoItem = Nodo::desde($rutaAntes);
                if($nodoItem->user_id!=$user->id) {
                    $errorCount++;
                    $errorMessages[] = "El item '$itemSource' no se pudo mover";
                    continue;
                }
            }

            // Verificar que el item exista
            if (!Storage::exists($itemSource)) {
                $errorCount++;
                $errorMessages[] = "El item '$itemSource' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if (Storage::directoryExists($itemSource)) {
                // Intentar mover la carpeta
                if (Storage::move($itemSource, $itemDestination)) {
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
                while (Storage::exists($itemDestination)) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $rutaDespues = $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $itemDestination = 'public/' . $rutaDespues;
                    $counter++;
                }

                if (Storage::move($itemSource, $itemDestination)) {
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
            $message = $successCount == 1 ? '1 item movido correctamente' : $successCount . ' items movidos correctamente';
            $response = ['message' => $message];
        } else {
            $response = ['error' => 'No se pudo mover ningún item'];
        }

        if ($errorCount > 0) {
            $response['errors'] = $errorMessages;
        }

        // Agregar registro de resumen a archivo de log
        Log::info("$successCount elementos movidos correctamente y $errorCount elementos fallidos");

        return response()->json($response, $successCount > 0 ? 200 : 500);
    }


    /**
     * Copia un conjunto de archivos a otra carpeta
     */
    public function copy(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $sourceFolder = $this->normalizarRuta($request->sourceFolder);
        $destinationFolder = $this->normalizarRuta($request->targetFolder);

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


        // comprobamos los permisos de lectura y escritura
        // $acl = Acl::from($user);
        $nodoSource = Nodo::desde($sourceFolder);
        $nodoDestination = Nodo::desde($destinationFolder);

        if (!$nodoSource || Gate::denies('leer', $nodoSource /*,  $acl*/)) {
            return response()->json([
                'error' => 'No tienes permisos para leer los archivos'
            ], 403);
        }
        if (!$nodoDestination || Gate::denies('escribir', $nodoDestination, $user)) {
            return response()->json([
                'error' => 'No tienes permisos para escribir'
            ], 403);
        }

        // Copiar cada item a la nueva carpeta de destino
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($items as $item) {
            $itemSource = 'public/' . $sourceFolder . "/" . $item;
            $itemDestination = 'public/' . $destinationFolder . "/" . $item;

            // Verificar que el item exista
            if (!Storage::exists($itemSource)) {
                $errorCount++;
                $errorMessages[] = "El item '$itemSource' no existe";
                continue;
            }

            // Verificar si el item es una carpeta
            if (Storage::directoryExists($itemSource)) {
                // Intentar copiar la carpeta
                if (Storage::copyDirectory($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de copia a archivo de log
                    Log::info("Carpeta '$item' copiada de '$sourceFolder' a '$destinationFolder'");
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar la carpeta '$itemSource'";
                }
            } else {
                // Verificar si el archivo de destino ya existe
                $counter = 1;
                while (Storage::exists($itemDestination)) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    $itemExtension = pathinfo($item, PATHINFO_EXTENSION);
                    $itemBaseName = $itemName . '_' . $counter;
                    $itemDestination = 'public/' . $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $counter++;
                }

                if (Storage::copy($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de copia a archivo de log
                    Log::info("Archivo '$item' copiado de '$sourceFolder' a '$destinationFolder'");
                } else {
                    $errorCount++;
                    $errorMessages[] = "No se pudo copiar el archivo '$itemSource'";
                }
            }
        }

        if ($successCount > 0) {
            $message = $successCount == 1 ? '1 item copiado correctamente' : $successCount . ' items copiados correctamente';
            $response = ['message' => $message];
        } else {
            $response = ['error' => 'No se pudo copiar ningún item'];
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

        if (strpos($ruta, 'storage') === 0) {
            $ruta = substr($ruta, 8);
        }
        return $ruta;
    }



}
