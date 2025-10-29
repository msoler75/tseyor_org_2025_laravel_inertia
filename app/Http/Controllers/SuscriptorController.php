<?php

namespace App\Http\Controllers;

use App\Models\Suscriptor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SuscriptorController extends Controller
{
    // Este método permite suscribir un usuario al boletín con un servicio por defecto o actualizar su suscripción existente.
    // Si no existe un token, se genera uno único.
    // Devuelve un mensaje de éxito o error según el resultado.
    public function suscribir(Request $request)
    {
        $boletin_por_defecto = config('app.suscripcion', 'boletin:mensual');

        $request->validate([
            'email' => 'required|email',
            'token' => 'string',
        ], [
            'email.required' => 'El campo de correo electrónico es obligatorio.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
        ]);

        $suscriptor = Suscriptor::where('email', $request->email)->first();

        if ($suscriptor) {
            $suscriptor->update([
                'servicio' => $boletin_por_defecto,
                'estado' => 'ok',
            ]);
            //  Suscriptor existente actualizado
            return response()->json(['message' => 'Suscripción exitosa']);
        }

        // podemos importar el token desde el request
        $token = $request->token;
        if(!$token)
            $token = bin2hex(random_bytes(16));

        // nos aseguramos que el token sea único
        while (Suscriptor::where('token', $token)->exists()) {
            $token = bin2hex(random_bytes(16));
        }

        try {
            Suscriptor::create([
                'email' => $request->email,
                'token' => $token,
                'servicio' => $boletin_por_defecto,
                'estado' => 'ok',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear suscriptor: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear suscriptor'], 500);
        }

        Log::info('Se ha creado suscriptor');

        return response()->json(['message' => 'Suscripción exitosa']);
    }

    // Este método permite desuscribir a un usuario del boletín utilizando su token único.
    // Si el token es válido, elimina al suscriptor y muestra una vista de desuscripción.
    public function desuscribir($token)
    {
        $suscriptor = Suscriptor::where('token', $token)->first();
        if($suscriptor)
            $suscriptor->delete();
        return Inertia::render('Boletines/Desuscripcion');
    }

    // Este método muestra una vista de confirmación de desuscripción.
    public function desuscripcionConfirmada()
    {
        return Inertia::render('Boletines/Desuscripcion');
    }

    public function mostrarConfiguracion($token)
    {
        $suscriptor = Suscriptor::where('token', $token)->first();
        $servicioActual = optional($suscriptor)->servicio;
        // Mostrar bisemanal como quincenal al usuario
        if ($servicioActual === 'boletin:bisemanal') {
            $servicioActual = 'boletin:quincenal';
        }
        return Inertia::render('Boletines/Config', [
            'servicioActual' => $servicioActual,
            'email' => optional($suscriptor)->email,
            'token' => $token,
        ]);
    }

    // Este método permite configurar la suscripción de un usuario al boletín.
    // Valida el servicio y el email, y actualiza o crea un registro de suscripción.
    // También permite dar de baja al usuario si selecciona "darse_baja".
    public function configurar(Request $request, $token)
    {
        $request->validate([
            'servicio' => 'string|in:boletin:semanal,boletin:bisemanal,boletin:mensual,boletin:bimensual,boletin:quincenal,darse_baja',
            'email' => 'email',
        ]);

        $servicio = $request->servicio ?? 'boletin:mensual';

        // Normalizar bisemanal a quincenal para compatibilidad
        if ($servicio === 'boletin:bisemanal') {
            $servicio = 'boletin:quincenal';
        }
        $suscriptor = Suscriptor::where('token', $token)->first();
        if(!$suscriptor) {
            $suscriptor = Suscriptor::create([
                'email' => $request->email,
                'token' => $token,
                'servicio' => $servicio,
                'estado' => 'ok',
            ]);
        }

        if ($servicio === 'darse_baja') {
            $suscriptor->delete();
            return response()->json(['message' => 'Te has dado de baja del boletín.']);
        }

        $suscriptor->servicio = $servicio;
        $suscriptor->save();

        return response()->json(['message' => 'Configuración actualizada correctamente.']);
    }

    // Este método devuelve la información de suscripción del boletín para el usuario autenticado.
    // Si el usuario no está autenticado o no tiene suscripción, devuelve un mensaje adecuado.
    public function getSuscripcion()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        return response()->json($user->boletin_suscripcion);
    }
}
