<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


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
}
