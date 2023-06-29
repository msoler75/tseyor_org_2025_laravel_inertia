<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\RadioItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lee el contenido del archivo radio.json
        $json = file_get_contents('d:\\tseyor.org\\admin\\data\\radio.json');

        // Decodifica el contenido del archivo a un objeto PHP
        $data = json_decode($json);

        // Recorre la playlist y crea un registro en la tabla radio por cada elemento
        foreach ($data->playlist as $audio => $info) {
            // Convierte la duración de minutos y segundos a segundos
            // Crea un nuevo objeto RadioItem con los datos del elemento actual
            $radioItem = new RadioItem([
                'audio' => "https://tseyor.org" . $audio,
                'duracion' => $info->duration,
                'categoria' => 'comunicados', // Puedes especificar la categoría que quieras aquí
                'desactivado' => 0 // Puedes establecer el valor de desactivado según tus necesidades
            ]);

            // Guarda el objeto en la base de datos
            $radioItem->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
