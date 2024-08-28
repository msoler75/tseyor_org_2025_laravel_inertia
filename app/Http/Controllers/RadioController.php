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
    public function index(Request $request)
    {

        $categorias = RadioItem::selectRaw('distinct categoria')->where('categoria', '<>', 'Jingles')->get()->pluck('categoria');

        return Inertia::render('Radio/Index', [
            'emisoras' => $categorias
        ]);
    }



    public function emisora(Request $request, $emisora)
    {
        // Iniciamos una transacción
        DB::beginTransaction();

        try {
            $emisoralwr = strtolower($emisora);
            $emisoralwr = filter_var($emisoralwr, FILTER_SANITIZE_ADD_SLASHES); // para evitar inyecciones de codigo
            $setting_name = 'radio_' . $emisoralwr;
            $setting = Setting::where('name', $setting_name)->first();

            try {
                $radio = $setting ? json_decode($setting->value, true) : null;
            } catch (\Exception $err) {
                Log::error('error al decodificar setting $setting_name');
            }

            if (!$radio)
                $radio = [
                    'reproduciendo_jingle' => false,
                    'audio_actual' => null,
                    'arranco_en' => 0,
                    'audio_siguiente' => null, // se utiliza para insertar el jingle
                    'jingle_idx' => null // índice del último jingle
                ];

            function convert_to_seconds($v)
            {
                if (is_int($v)) return $v;
                if (!preg_match("/:/", $v)) return intval($v);
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

            // dd($radio);

            // dd($audio_actual, $radio['audio_siguiente']);

            $duracion_actual = convert_to_seconds($audio_actual['duracion'] ?? 0);
            //if($duracion_actual > 15) $duracion_actual = 15;

            // cambiamos ya a la siguiente pista de audio?
            if (!$audio_actual || $now - $arranco_en + 3 > $duracion_actual) {

                if ($radio['audio_siguiente'] ?? null) {
                    $audio_actual = $radio['audio_siguiente'];
                    // $radio['audio_actual'] = $audio_actual;
                    $radio['reproduciendo_jingle'] = false;
                    unset($radio['audio_siguiente']);
                } else {
                    // buscamos el siguiente de la lista de esta emisora/categoría

                    if ($audio_actual)
                        $audio_actual = RadioItem::whereRaw("LOWER(categoria) LIKE '%$emisoralwr%'")->whereNull("desactivado")->where('id', '>', $audio_actual['id'])->orderBy('id', 'asc')->first();
                    if (!$audio_actual) $audio_actual = RadioItem::whereRaw("LOWER(categoria) LIKE '%$emisoralwr%'")->whereNull("desactivado")->orderBy('id', 'asc')->first();

                    if (!$audio_actual) {
                        Log::error("No se pudo obtener un nuevo audio de la radio en emisora $emisora");
                        throw new \Exception("No se pudo obtener un nuevo audio de la radio en emisora $emisora");
                    }

                    $audio_actual = $audio_actual->toArray();

                    // obtenemos los jingles
                    $jingles  = RadioItem::where("categoria", 'Jingles')->get()->toArray();

                    if (count($jingles)) {
                        //ponemos un jingle entre cada audio
                        $radio['audio_siguiente'] = $audio_actual;
                        $radio['reproduciendo_jingle'] = true;
                        $j = $radio['jingle_idx'] ?? -1;
                        $j = ($j + 1) % count($jingles);
                        $jingle = $jingles[$j];
                        $radio['jingle_idx'] = $j;
                        $audio_actual = $jingle;
                    }

                    $radio['audio_actual'] = $audio_actual;

                    $modified = true;
                }

                $radio['audio_actual'] = $audio_actual;
                $radio['arranco_en'] = $now;
                $modified = true;
            }


            if ($modified) {
                $json_radio = json_encode($radio, JSON_PRETTY_PRINT);
                Setting::updateOrCreate(['name' => $setting_name], ['value' => $json_radio]);
            }

            // Realiza las operaciones dentro de la transacción aquí
            DB::commit();

            $duracion_actual = convert_to_seconds($radio['audio_actual']['duracion']);
            //if($duracion_actual>15) $duracion_actual= 15;

            $estado = [
                'emisora' => $emisora,
                'audio_actual' => $radio['audio_actual'],
                'es_jingle' => $radio['reproduciendo_jingle'],
                'duracion_en_segundos' => $duracion_actual,
                'tiempo_sistema' => $now,
                'arranco_en' => $radio['arranco_en'],
                'termina_en' => $radio['arranco_en'] + $duracion_actual,
                'posicion_actual' => $now - $radio['arranco_en'],
                'segundos_restantes' => ($radio['arranco_en'] + $duracion_actual) - $now,
            ];

            if ($radio['reproduciendo_jingle'])
                $estado['audio_siguiente'] = $radio['audio_siguiente'] ?? null;

            $categorias = RadioItem::selectRaw('distinct categoria')->where('categoria', '<>', 'Jingles')->get()->pluck('categoria');

            return Inertia::render('Radio/Emisora', [
                'estado' => $estado,
                'emisoras' => $categorias
            ])
                ->withViewData(SEO::get('radio'));
        } catch (\Exception $e) {
            // Si se produce un error, deshace los cambios realizados durante la transacción
            DB::rollBack();
            throw $e;
        }
    }
}
