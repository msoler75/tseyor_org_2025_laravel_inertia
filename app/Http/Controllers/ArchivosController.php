<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use App\Policies\LinuxPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Nodo;


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
        $ruta = $request->path();

        return $this->list($ruta, false);
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
        $ruta = ltrim($ruta, '/');

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

        $user = auth()->user();

        $acl = LinuxPolicy::acl($user, ['ejecutar', 'escribir']);

        // comprobamos el permiso de ejecución (listar) en la carpeta
        $nodo = LinuxPolicy::nodoHeredado($ruta);


        if (!LinuxPolicy::ejecutar($nodo, $user, $acl)) {
            throw new AuthorizationException('No tienes permisos para ver la carpeta', 403);
        }

        $rutaPadre = dirname($rutaBase);

        $items = [];

        // elemento de carpeta actual
        $archivos_internos = Storage::disk('public')->files($ruta);
        $subcarpetas_internas = Storage::disk('public')->directories($ruta);

        $item = [
            'nombre' => basename($ruta),
            'actual' => true,
            'tipo' => 'carpeta',
            'ruta' => $rutaBase,
            'url' => $rutaBase,
            'carpeta' => $rutaPadre,
            'archivos' => count($archivos_internos),
            'subcarpetas' => count($subcarpetas_internas),
            'fecha_modificacion' => Storage::disk('public')->lastModified($ruta),
        ];


        $nodoCarpeta = $nodo;
        $item['permisos'] =  optional($nodo)->permisos ?? 0;
        $item['propietario'] = ['usuario' => optional($nodo)->propietario_usuario, 'grupo' => optional($nodo)->propietario_grupo];
        $items[] = $item;


        // Agregar elemento de carpeta padre
        $padre = dirname($ruta);
        if ($padre && $padre != "\\" && $padre != "//") {
            $archivos_internos = Storage::disk('public')->files($ruta);
            $subcarpetas_internas = Storage::disk('public')->directories($ruta);

            $item = [
                'nombre' => basename($ruta),
                'padre' => true,
                'tipo' => 'carpeta',
                'ruta' => $rutaPadre,
                'url' => $rutaPadre,
                'carpeta' => $rutaPadre,
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($padre),
            ];

            $nodo = LinuxPolicy::nodoHeredado($padre);
            if (!$nodo || !LinuxPolicy::ejecutar($nodo, $user, $acl))
                $item['privada'] = true;

            $item['permisos'] =  optional($nodo)->permisos ?? 0;
            $item['propietario'] = ['usuario' => optional($nodo)->propietario_usuario, 'grupo' => optional($nodo)->propietario_grupo];

            $items[] = $item;
        }

        // Agregar carpetas a la colección de elementos
        $carpetas = Storage::disk('public')->directories($ruta);

        // obtenemos todos los nodos de la carpeta
        $nodos = LinuxPolicy::nodosCarpeta($ruta);

        foreach ($carpetas as $carpeta) {
            $archivos_internos = Storage::disk('public')->files($carpeta);
            $subcarpetas_internas = Storage::disk('public')->directories($carpeta);
            $urlItem = str_replace($baseUrl, '',  str_replace('/storage', '', Storage::disk('public')->url($carpeta)));
            $item = [
                'nombre' => basename($carpeta),
                'clase' => '',
                'tipo' => 'carpeta',
                'ruta' => $urlItem,
                'url' => $urlItem,
                'carpeta' => $rutaBase,
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($carpeta)
            ];
            // Obtener información de la carpeta correspondiente, si existe

            $nodo = $nodos->where('ruta', $ruta . "/" . $item['nombre'])->first();
            if (!$nodo)
                $nodo = $nodoCarpeta;
            //if($nodo->ruta=='archivos/salud')
            //  dd($nodo);
            if (!$nodo || !LinuxPolicy::ejecutar($nodo, $user, $acl))
                $item['privada'] = true;
            $item['permisos'] =  optional($nodo)->permisos ?? 0;
            $item['propietario'] = ['usuario' => optional($nodo)->propietario_usuario, 'grupo' => optional($nodo)->propietario_grupo];

            $items[] = $item;
        }

        // Agregar archivos a la colección de elementos
        $archivos = Storage::disk('public')->files($ruta);
        foreach ($archivos as $archivo) {
            $urlItem = str_replace($baseUrl, '', Storage::disk('public')->url($ruta . '/' . basename($archivo)));
            $item = [
                'nombre' => basename($archivo),
                'clase' => '',
                'tipo' => 'archivo',
                'ruta' => $urlItem,
                'url' => $urlItem,
                'carpeta' => $rutaBase,
                'tamano' => Storage::disk('public')->size($archivo),
                'fecha_modificacion' => Storage::disk('public')->lastModified($archivo),
            ];

            $nodo = $nodos->where('ruta', $ruta . "/" . $item['nombre'])->first();
            if (!$nodo)
                $nodo = $nodoCarpeta;
            $item['permisos'] =  optional($nodo)->permisos ?? 0;
            $item['propietario'] = ['usuario' => optional($nodo)->propietario_usuario, 'grupo' => optional($nodo)->propietario_grupo];

            $items[] = $item;
        }


        // comprobamos los permisos de escritura (para saber si puede crear carpetas, o renombrar archivos)
        $puedeEscribir = LinuxPolicy::escribir($nodoCarpeta, $user, $acl);

        $propietario = null;

        // comprobamos si la carpeta es de un equipo, o de un usuario, en cuyo caso buscamos la forma de referenciarlo
        $equipo = Equipo::where('group_id', $nodoCarpeta->group_id)->first();
        if ($equipo)
            $propietario = [
                'url' => route('equipo', $equipo->slug || $equipo->id),
                'nombre' => $equipo->nombre,
                'tipo' => 'equipo'
            ];
        else {
            $usuario = User::where('group_id', $nodoCarpeta->user_id)->first();
            $propietario = [
                'url' => route('usuario', $usuario->slug || $usuario->id),
                'nombre' => $usuario->name,
                'tipo' => 'usuario'
            ];
        }

        $respuesta =  [
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

        // Crear la carpeta si no existe
        $path = storage_path("app/" . $folder);
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
        LinuxPolicy::crearNodo(auth()->user(), $folder . '/' . $filename);

        // Obtener la URL pública del archivo
        $url = Storage::url($storedPath);

        return response()->json([
            'data' => [
                'filePath' => substr($url, 1)
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
        $folder = $this->normalizarRuta($request->destinationPath);

        // detecta si estamos editando un tipo de datos y lo extrae, para asignarle después una carpeta
        $url = $request->headers->get('referer');
        if ($url && preg_match('/admin\/(.*?)\/\d+\/edit/', $url, $matches)) {
            $folder = $matches[1];
        } else {
            // $folder = null;
        }

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
        $acl = LinuxPolicy::acl($user);
        $nodo = LinuxPolicy::nodoHeredado($folder);
        if (!$nodo || !LinuxPolicy::escribir($nodo, $user, $acl)) {
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

        // Creamos el nodo
        LinuxPolicy::crearNodo($user, $folder . '/' . $name, true);

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
        $acl = LinuxPolicy::acl($user);
        $nodo = LinuxPolicy::nodoHeredado($ruta);
        if (!$nodo || !LinuxPolicy::escribir($nodo, $user, $acl)) {
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
     * Renombra un archuvo que está en una carpeta $folder de viejo nombre $oldName a $newName
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


        // comprobamos los permisos de escritura
        $acl = LinuxPolicy::acl($user);
        $nodo = LinuxPolicy::nodoHeredado($rutaAntes);
        if (!$nodo || !LinuxPolicy::escribir($nodo, $user, $acl)) {
            return response()->json([
                'error' => 'No tienes permisos'
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
        LinuxPolicy::move($rutaAntes, $rutaDespues);

        return $response;
    }


    /**
     * Mueve un conjunto de archivos a otra carpeta
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
        $acl = LinuxPolicy::acl($user);
        $nodoSource = LinuxPolicy::nodoHeredado($sourceFolder);
        $nodoDestination = LinuxPolicy::nodoHeredado($destinationFolder);

        // dd($nodoSource);

        if (!$nodoSource || !LinuxPolicy::leer($nodoSource, $user, $acl)) {
            return response()->json([
                'error' => 'No tienes permisos de lectura en la carpeta origen'
            ], 403);
        }
        if (!LinuxPolicy::escribir($nodoSource, $user, $acl)) {
            return response()->json([
                'error' => 'No tienes permisos de escritura en la carpeta origen'
            ], 403);
        }
        if (!$nodoDestination || !LinuxPolicy::escribir($nodoDestination, $user, $acl)) {
            return response()->json([
                'error' => 'No tienes permisos en la carpeta destino'
            ], 403);
        }


        // Mover cada item a la nueva carpeta de destino
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($items as $item) {
            $rutaAntes =  $sourceFolder . "/" . $item;
            $rutaDespues =  $destinationFolder . "/" . $item;
            $itemSource = 'public/' . $rutaAntes;
            $itemDestination = 'public/' . $rutaDespues;

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
                    LinuxPolicy::move($rutaAntes, $rutaDespues);
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
                    $rutaDespues =  $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $itemDestination = 'public/' . $rutaDespues;
                    $counter++;
                }

                if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Archivo '$item' movido de '$sourceFolder' a '$destinationFolder'");

                    // debemos renombrar todas las rutas afectadas en los nodos
                    LinuxPolicy::move($rutaAntes, $rutaDespues);
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
        $acl = LinuxPolicy::acl($user);
        $nodoSource = LinuxPolicy::nodoHeredado($sourceFolder);
        $nodoDestination = LinuxPolicy::nodoHeredado($destinationFolder);

        if (!$nodoSource || !LinuxPolicy::leer($nodoSource, $user, $acl)) {
            return response()->json([
                'error' => 'No tienes permisos para leer los archivos'
            ], 403);
        }
        if (!$nodoDestination || !LinuxPolicy::escribir($nodoDestination, $user, $acl)) {
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
            return substr($ruta, 1);
        }
        return $ruta;
    }
}
