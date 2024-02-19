<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatGPTController extends Controller
{
    //
    public function chat(Request $request)
    {
        $texto = $request->input("texto");

        if (!$texto)
            abort(400, "Debe especificar el texto");

        $apikey = config("services.openai_key", null);
        if (!$apikey)
            abort(400, "No se ha configurado la clave API de OpenAI");

        $opts = [
            // "model" => "dall-e-2",
            "model" => 'gpt-3.5-turbo-0125',
            // "object"=> "chat.completion",
            "messages" => [
                [
                    "role" => "system",
                    "content" => 'Eres un asistente util que da siempre respuestas breves y concisas, sin explicaciones, a menos que se te pidan.'
                ],
                [
                    "role" => "user",
                    "content" => $texto
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apikey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($opts));

        $response = curl_exec($ch);

        // get status http respose:
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // dd($response);

        curl_close($ch);

        return response()->json(['respuesta' => $response, 'status_code'=>$status], 200);

    }
}
