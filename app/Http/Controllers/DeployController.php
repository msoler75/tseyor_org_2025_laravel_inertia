<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use ZipArchive;
use App\Pigmalion\DeployHelper as Deploy;

/**
 *
 */
class DeployController extends Controller
{

    /**
     * Recoge el archivo .zip de la carpeta public/build (borrando si hubiera uno previo)
     * Descomprime el archivo en destino public/build_temp (borrando la previa)
     * Borra la carpeta public/build_old
     * Renombra la carpeta public/build a public/build_old
     * Renombra la carpeta public/build_temp a public/build
     */
    public function handlePublicBuildUpload(Request $request)
    {

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha enviado ningún archivo'], 400);
        }

        try {
            Log::channel('deploy')->info("Instalando nueva versión de FrontEnd");

            $zipPath = Deploy::storeUploadedFile($request, 'public_build');

            // Si llega el flag 'prepare', simplemente mover el ZIP a storage/install y terminar
            if ($request->has('prepare')) {
                $dest = Deploy::moveZipToInstall($zipPath);
                return response()->json(['message' => 'Archivo guardado en carpeta de instalación', 'path' => $dest], 200);
            }

            // Delegar la instalación específica al método apropiado
            Deploy::installPublicBuildFromZip($zipPath);

            return response()->json(['message' => 'Build actualizado correctamente'], 200);
        } catch (\Exception $exception) {
            Log::channel('deploy')->error('Error en despliegue de public/build: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Rollback del build público: restaura la versión anterior
     */
    public function rollbackPublicBuild(Request $request)
    {
        try {
            Log::channel('deploy')->info("Rollback de FrontEnd a versión anterior");

            Deploy::rollbackPublicBuild();

            return response()->json(['message' => 'Rollback realizado correctamente'], 200);
        } catch (\Exception $exception) {
            Log::channel('deploy')->error('Error en rollback de public/build: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }


    public function handleSSRUpload(Request $request)
    {

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha proporcionado un archivo .zip'], 400);
        }

        try {

            $zipPath = Deploy::storeUploadedFile($request, 'ssr');

            // Si llega el flag 'prepare', simplemente mover el ZIP a storage/install y terminar
            if ($request->has('prepare')) {
                $dest = Deploy::moveZipToInstall($zipPath);
                return response()->json(['message' => 'Archivo guardado en carpeta de instalación', 'path' => $dest], 200);
            }

            // Delegar la instalación específica al método apropiado
            Deploy::installSSRFromZip($zipPath);

            return response()->json(['message' => 'Archivos SSR extraídos correctamente'], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }



    // handle node_modules uploading
    public function handleNodeModulesUpload(Request $request)
    {
        // Verificar si se ha enviado un archivo
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha enviado ningún archivo'], 400);
        }

        try {
            $zipPath = Deploy::storeUploadedFile($request, 'node_modules');

            // Si llega el flag 'prepare', simplemente mover el ZIP a storage/install y terminar
            if ($request->has('prepare')) {
                $dest = Deploy::moveZipToInstall($zipPath);
                return response()->json(['message' => 'Archivo guardado en carpeta de instalación', 'path' => $dest], 200);
            }

            // Determinar si es un despliegue de paquete específico
            $isPackage = $request->query('type') === 'package';

            // Ejecutar instalación específica (backup solo si no es paquete, extracción, limpieza)
            Deploy::installNodeModulesFromZip($zipPath, $isPackage);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::channel('deploy')->error('Error en despliegue: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } finally {


            // 10. Limpieza de archivos temporales
            //Deploy::cleanTempFiles($zipPath ?? null);
        }
    }





}
