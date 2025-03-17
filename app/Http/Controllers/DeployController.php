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
        // Verificar si se ha enviado un archivo
        //dd($request->allFiles());
        //dd($request->file('file'));


        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha enviado ningún archivo'], 400);
        }

        try {

            Log::channel('deploy')->info("Instalando nueva versión de FrontEnd");

            $zipPath = Deploy::storeUploadedFile($request, 'public_build');

            // Definir rutas
            $buildPath = public_path('build');
            $buildTempPath = public_path('build_temp');
            $buildOldPath = public_path('build_old');

            Log::channel('deploy')->info("Paths: ", ['buildPath' => $buildPath, 'buildTempPath' => $buildTempPath, 'buildOldPath' => $buildOldPath]);

            // Eliminar build_temp si existe
            if (File::isDirectory($buildTempPath)) {
                File::deleteDirectory($buildTempPath);
            }

            // Descomprimir el archivo en build_temp
            Deploy::extractZip($zipPath, $buildTempPath);

            // Replacement de localhost a APP_URL
            $files = Deploy::doReplacements( 'public/build_temp/assets/iconify*.js',
                                                'http://localhost',
                                                config('app.url'));
            if(count($files))
                Log::channel('deploy')->info("Archivos actualizados: ", $files);

            // Borrar la carpeta build_old si existe
            if (File::isDirectory($buildOldPath)) {
                Log::channel('deploy')->info("Borramos carpeta " . $buildOldPath);
                File::deleteDirectory($buildOldPath);
            }

            // Renombrar build a build_old
            if (File::isDirectory($buildPath)) {
                Log::channel('deploy')->info("Renombramos " . $buildPath . " a " . $buildOldPath);
                File::move($buildPath, $buildOldPath);
            }

            // Renombrar build_temp a build
            Log::channel('deploy')->info("Renombramos " . $buildTempPath . " a " . $buildPath);
            File::move($buildTempPath, $buildPath);

            // Eliminar el archivo ZIP temporal
            // File::delete($zipPath);

            return response()->json(['message' => 'Build actualizado correctamente'], 200);
        } catch (\Exception $exception) {
            Log::channel('deploy')->error('Error en despliegue de public/build: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()]);
        }
    }


    public function handleSSRUpload(Request $request)
    {

        if (!$this->validateRequest($request)) {
            return response('Acceso no autorizado', 403);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha proporcionado un archivo .zip'], 400);
        }

        try {

            $zipPath = Deploy::storeUploadedFile($request, 'ssr');
            $extractPath = base_path('bootstrap/ssr');

            // recrea la carpeta bootstrap/ssr
            File::deleteDirectory($extractPath);
            File::makeDirectory($extractPath, 0755, true, true);

            // Extrae el contenido del .zip en bootstrap/ssr
            Deploy::extractZip($zipPath, $extractPath);

            // Replacement de localhost a APP_URL
            $files = Deploy::doReplacements( 'bootstrap/ssr/ssr.js',
                                                'http://localhost',
                                                config('app.url'));
            if(count($files))
                Log::channel('deploy')->info("Archivos actualizados: ", $files);

            // Verifica que los archivos necesarios existen
            if (File::exists($extractPath . '/ssr.js') && File::exists($extractPath . '/ssr-manifest.json')) {
                return response()->json(['message' => 'Archivos SSR extraídos correctamente'], 200);
            } else {
                return response()->json(['error' => 'El archivo .zip no contiene los archivos SSR necesarios'], 400);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }



    // handle node_modules uploading
    public function handleNodeModulesUpload(Request $request)
    {
        if (!$this->validateRequest($request)) {
            return response('Acceso no autorizado', 403);
        }

        // Verificar si se ha enviado un archivo
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha enviado ningún archivo'], 400);
        }

        try {
            // 1. Crear backup de node_modules existente
            $backupPath = Deploy::backupNodeModules();

            // 4. Limpiar node_modules existente
            Deploy::cleanNodeModules();

            // 5. Manejar el archivo ZIP
            $zipPath = Deploy::storeUploadedFile($request, 'node_modules');

            // 6. Descomprimir con verificación
            Deploy::extractZip($zipPath, base_path());

            // 7. Limpieza post-instalación
            Deploy::postInstallCleanup();

            // 8. Limpiar backups antiguos
            Deploy::cleanOldBackups();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::channel('deploy')->error('Error en despliegue: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        } finally {


            // 10. Limpieza de archivos temporales
            Deploy::cleanTempFiles($zipPath ?? null);
        }
    }

    private function validateRequest(Request $request): bool
    {
        return true;
        // return $request->header('X-Deploy-Token') === config('app.deploy_token');
    }
}
