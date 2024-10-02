<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use ZipArchive;


/**
 *
 */
class DevController extends Controller
{
    /**
     * Logs in a user.
     *
     * @return JsonResponse JSON response with a success message
     */
    public function loginUser1(): JsonResponse
    {
        $user = User::find(2);
        Auth::login($user); // Autenticar al usuario 3
        return response()->json(['message' => 'usuario cambiado'], 200);
    }

    public function loginUser2()
    {
        $user = User::find(3); // Obtener usuario 2 de la base de datos
        Auth::login($user); // Autenticar al usuario 2
        return response()->json(['message' => 'usuario cambiado'], 200);
    }


    public function dev1(Request $request)
    {
        $user = User::find(1);
        echo "Enviando notificacion a user {$user->name} ";
        $user->sendEmailVerificationNotification();
    }


    public function dev2(Request $request)
    {
       $user =  $this->getUser("pepitito");
       dd($user->toArray());
    }


    protected function getUser($nombre)
    {
        // $faker = \Faker\Factory::create();
        $user = User::where('name', $nombre)->first();
        if (!$user)
            $user = User::create(['name' => $nombre, 'email'=>$nombre.'@gmaix.co', 'slug'=> Str::slug($nombre), 'password' => '123456678']);
        return $user;


    }



    /**
     * Recoge el archivo .zip de la carpeta public/build (borrando si hubiera uno previo)
     * Descomprime el archivo en destino public/build_temp (borrando la previa)
     * Borra la carpeta public/build_old
     * Renombra la carpeta public/build a public/build_old
     * Renombra la carpeta public/build_temp a public/build
     */
    public function newBuild(Request $request)
    {
        //omitimos comprobación CRSF

        // Verificar si se ha enviado un archivo
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se ha enviado ningún archivo'], 400);
        }

        $file = $request->file('file');
        $zipPath = storage_path('app/temp_build.zip');

        // return response()->json(['zip:'=>$zipPath]);

        // borramos si hubiera un zip previo
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        // Mover el archivo subido a una ubicación temporal
        $file->move(storage_path('app'), 'temp_build.zip');

        // Definir rutas
        $buildPath = public_path('build');
        $buildTempPath = public_path('build_temp');
        $buildOldPath = public_path('build_old');

        // Descomprimir el archivo en build_temp
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            // Eliminar build_temp si existe
            if (File::isDirectory($buildTempPath)) {
                File::deleteDirectory($buildTempPath);
            }

            // Crear build_temp y descomprimir
            File::makeDirectory($buildTempPath);
            $zip->extractTo($buildTempPath);
            $zip->close();

            // Borrar la carpeta build_old si existe
            if (File::isDirectory($buildOldPath)) {
                File::deleteDirectory($buildOldPath);
            }

            // Renombrar build a build_old
            if (File::isDirectory($buildPath)) {
                File::move($buildPath, $buildOldPath);
            }

            // Renombrar build_temp a build
            File::move($buildTempPath, $buildPath);

            // Eliminar el archivo ZIP temporal
            File::delete($zipPath);

            return response()->json(['message' => 'Build actualizado correctamente'], 200);
        } else {
            return response()->json(['error' => 'No se pudo abrir el archivo ZIP'], 500);
        }
    }
}
