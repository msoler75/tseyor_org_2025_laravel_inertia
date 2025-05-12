<?php

namespace App\Http\Controllers;

use App\Models\Suscriptor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SuscriptorController extends Controller
{
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

    public function desuscribir($token)
    {
        $suscriptor = Suscriptor::where('token', $token)->first();
        if($suscriptor)
            $suscriptor->delete();
        return Inertia::render('Boletines/Desuscripcion');
    }

    public function desuscripcionConfirmada()
    {
        return Inertia::render('Boletines/Desuscripcion');
    }

    public function mostrarConfiguracion($token)
    {
        $suscriptor = Suscriptor::where('token', $token)->first();
        return Inertia::render('Boletines/Config', [
            'servicioActual' => optional($suscriptor)->servicio,
            'email' => optional($suscriptor)->email,
            'token' => $token,
        ]);
    }

    public function configurar(Request $request, $token)
    {
        $request->validate([
            'servicio' => 'string|in:boletin:semanal,boletin:bisemanal,boletin:mensual,boletin:bimensual,darse_baja',
            'email' => 'email',
        ]);

        $servicio = $request->servicio ?? 'boletin:mensual';
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
}
