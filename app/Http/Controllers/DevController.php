<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


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


}
