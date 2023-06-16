<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class FilesController extends Controller
{

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

        $deniedTypes = ['exe', 'cmd', 'php', 'js'];

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
        $folder =  $request->destinationPath;

        return $this->processUpload($request, $file, $folder);
    }

    // viene del markdown editor
    public function uploadImage(Request $request)
    {
        $file = $request->file('image');
        $folder = $request->destinationPath;

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


    public function createFolder(Request $request)
    {
        $folder = $request->folder;
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

        if (!mkdir($newFolderPath, 0755)) {
            return response()->json([
                'error' => 'unableToCreateFolder'
            ], 500);
        }

        return response()->json([
            'message' => 'folderCreated'
        ], 200);
    }


    // Elimina un item, indicado en ruta
    public function delete($ruta)
    {
        // Concatenar la ruta completa al archivo
        $archivo = 'public/' . $ruta;

        // Verificar si la ruta contiene saltos de carpeta
        if ($ruta == ".." || strpos($ruta, "../") !== false || strpos($ruta, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        // Verificar que el archivo exista
        if (!Storage::exists($archivo)) {
            return response()->json(['error' => 'El archivo no existe'], 404);
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


    public function rename(Request $request)
    {
        $folder = $request->folder;
        $oldName = $request->oldName;
        $newName = $request->newName;


        // Verificar si faltan parámetros
        if (!$request->filled(['folder', 'oldName', 'newName'])) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }


        if ($folder == ".." || strpos($folder, "../") !== false || strpos($folder, "/..") !== false) {
            return response()->json(['error' => 'No se permiten saltos de carpeta'], 400);
        }

        $itemAntes = 'public/' . $folder . "/" . $oldName;
        $itemDespues = 'public/' . $folder . "/" . $newName;

        // Verificar que el item exista
        if (!Storage::exists($itemAntes)) {
            return response()->json(['error' => "El item '$itemAntes' no existe"], 404);
        }

        // Verificar si el item es una carpeta
        if (Storage::directoryExists($itemAntes)) {
            // Intentar renombrar la carpeta
            if (Storage::move($itemAntes, $itemDespues)) {
                return response()->json(['message' => 'Carpeta renombrada correctamente'], 200);
            } else {
                return response()->json(['error' => 'No se pudo renombrar la carpeta'], 500);
            }
        } else {
            // Intentar renombrar el archivo
            if (Storage::rename($itemAntes, $itemDespues)) {
                return response()->json(['message' => 'Archivo renombrado correctamente'], 200);
            } else {
                return response()->json(['error' => 'No se pudo renombrar el archivo'], 500);
            }
        }
    }



    public function move(Request $request)
    {
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

        Log::info("Items a mover: " . var_export($items, true));

        // Mover cada item a la nueva carpeta de destino
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
                // Intentar mover la carpeta
                if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Carpeta '$item' movida de '$sourceFolder' a '$destinationFolder'");
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
                    $itemDestination = 'public/' . $destinationFolder . "/" . $itemBaseName . '.' . $itemExtension;
                    $counter++;
                }

                // Intentar mover el archivo
                Log::info("Trataremos de mover de $itemSource a $itemDestination");
                if (Storage::move($itemSource, $itemDestination)) {
                    $successCount++;
                    // Agregar registro de movimiento a archivo de log
                    Log::info("Archivo '$item' movido de '$sourceFolder' a '$destinationFolder'");
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


    public function copy(Request $request)
    {
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

        Log::info("Items a copiar: " . var_export($items, true));

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

                // Intentar copiar el archivo
                Log::info("Trataremos de copiar de $itemSource a $itemDestination");
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
}
