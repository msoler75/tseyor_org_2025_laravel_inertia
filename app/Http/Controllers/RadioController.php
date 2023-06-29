<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Setting;
use App\Models\RadioItem;
use App\Pigmalion\SEO;

class RadioController extends Controller
{
    public function index()
    {
        // Iniciamos una transacción
        DB::beginTransaction();

        try {

            $categoria = 'comunicados';
            $setting_name = 'radio_' . $categoria;
            $setting = Setting::find($setting_name);

            try {
                $radio = $setting ? json_decode($setting->value, true) : null;
            } catch (\Exception $err) {
                Log::warning('error al decodificar setting  $setting_name');
            }

            if (!$radio)
                $radio = [
                    'reproduciendo_jingle' => false,
                    'audio_actual' => null,
                    'current_duracion' => 0,
                    'arranco_en' => 0,
                    'siguiente_audio' => null, // se utiliza para insertar el jingle
                    'ultimo_jingle' => null
                ];

            function convert_to_seconds($v)
            {
                $t = explode(':', $v);
                return intval(end($t)) + intval(prev($t)) * 60 + intval(prev($t)) * 3600;
            }

            // marcamos si hemos de actualizar el setting
            $modified = false;

            // tomamos el tiempo actual
            $now = time();

            // gestión de playlist
            $audio_actual =  $radio['audio_actual'] ?? null;
            $arranco_en = $radio['arranco_en'] ?? 0;

            // cambiamos ya a la siguiente pista de audio?
            if (!$audio_actual || $now - $arranco_en + 3 > convert_to_seconds($audio_actual['duracion'] ?? 0)) {

                if ($radio['siguiente_audio'] ?? null) {
                    $audio_actual = $radio['siguiente_audio'];
                    $radio['audio_actual'] = $audio_actual;
                    $radio['reproduciendo_jingle'] = false;
                    unset($radio['siguiente_audio']);
                } else {

                    // obtenemos la playlist
                    $playlist = RadioItem::select(['id', 'audio', 'duracion'])->where("categoria", $categoria)->get()->toArray();

                    // buscamos el indice del audio actual
                    $idx = 0;
                    if ($audio_actual) {
                        foreach ($playlist as $indice => $item) {
                            if ($item['audio'] === $audio_actual) {
                                $idx = $indice;
                                break;
                            }
                        }
                    }

                    // aumentamos el indice idx para ir al siguiente audio
                    $idx = ($idx + 1) % count($playlist);
                    $audio_actual = $playlist[$idx];

                    // obtenemos los jingles
                    $jingles  = RadioItem::select(['id', 'audio', 'duracion'])->where("categoria", 'jingles')->get()->toArray();

                    if (count($jingles)) {
                        //ponemos un jingle entre cada audio
                        $radio['siguiente_audio'] = $audio_actual;
                        $radio['reproduciendo_jingle'] = true;
                        $j = $radio['ultimo_jingle'] ?? -1;
                        $j = ($j + 1) % count($jingles);
                        $jingle = $jingles[$j];
                        $radio['ultimo_jingle'] = $j;
                        $audio_actual = $jingle;
                    }

                    $radio['audio_actual'] = $audio_actual;
                }

                $radio['audio_actual'] = $audio_actual;
                $radio['arranco_en'] = $now;
                $modified = true;
            }


            if ($modified) {
                $json_radio = json_encode($radio);
                Setting::updateOrCreate(['name' => $setting_name], ['value' => $json_radio]);
            }

            // Realiza las operaciones dentro de la transacción aquí
            DB::commit();

            $estado = [
                'audio_actual' => $radio['audio_actual']['audio'],
                'duracion_segundos' => convert_to_seconds($radio['audio_actual']['duracion']),
                'arranco_en' => $radio['arranco_en'],
                'termina_en' => $radio['arranco_en'] + convert_to_seconds($radio['audio_actual']['duracion']),
                'tiempo_sistema' => $now,
                'es_jingle' => $radio['reproduciendo_jingle'],
            ];

            if ($radio['reproduciendo_jingle'])
                $estado['siguiente_audio'] = $radio['siguiente_audio']['audio'];

            return Inertia::render('Radio/Index', ['estado' => $estado])
                ->withViewData(SEO::get('radio'));
        } catch (\Exception $e) {
            // Si se produce un error, deshace los cambios realizados durante la transacción
            DB::rollBack();
            throw $e;
        }
    }
}
