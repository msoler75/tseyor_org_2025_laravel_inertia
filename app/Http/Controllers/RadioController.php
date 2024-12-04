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
    /**
     * Mostrar página de índice de radio con emisoras disponibles
     */
    public function index(Request $request)
    {
        $categorias = $this->obtenerCategoriasEmisoras();

        return Inertia::render('Radio/Index', [
            'emisoras' => $categorias
        ]);
    }

    /**
     * Manejar la lógica de reproducción de una emisora
     */
    public function emisora(Request $request, $emisora)
    {
        DB::beginTransaction();

        try {
            $estado = $this->gestionarEstadoRadio($emisora);

            DB::commit();

            return $this->renderizarVistaEmisora($estado);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Gestionar el estado completo de la radio
     */
    private function gestionarEstadoRadio(string $emisora): array
    {
        // Preparar información de la emisora
        $emisoraLwr = $this->sanearNombreEmisora($emisora);
        $nombreConfiguracion = 'radio_' . $emisoraLwr;

        // Obtener o inicializar estado de la radio
        $radio = $this->obtenerConfiguracionRadio($nombreConfiguracion);

        // Determinar si es necesario cambiar el audio
        $modificado = $this->verificarCambioAudio($radio, $emisoraLwr);

        // Guardar configuración si ha cambiado
        if ($modificado) {
            $this->guardarConfiguracionRadio($nombreConfiguracion, $radio);
        }

        // Preparar estado para respuesta
        return $this->generarEstadoRadio($radio, $emisora);
    }

    /**
     * Sanear el nombre de la emisora para prevenir inyección
     */
    private function sanearNombreEmisora(string $emisora): string
    {
        $emisoraLwr = strtolower($emisora);
        return filter_var($emisoraLwr, FILTER_SANITIZE_ADD_SLASHES);
    }

    /**
     * Obtener o inicializar la configuración de radio
     */
    private function obtenerConfiguracionRadio(string $nombreConfiguracion): array
    {
        try {
            $configuracion = Setting::where('name', $nombreConfiguracion)->first();
            $radio = $configuracion ? json_decode($configuracion->value, true) : null;
        } catch (\Exception $err) {
            Log::error('Error al decodificar configuración ' . $nombreConfiguracion);
            $radio = null;
        }


        // Estado inicial por defecto
        return $radio ?: [
            'reproduciendo_jingle' => false,
            'audio_actual' => null,
            'arranco_en' => now()->timestamp,
            'audio_siguiente' => null,
            'jingle_idx' => null
        ];
    }

    /**
     * Verificar si es necesario cambiar el audio
     */
    private function verificarCambioAudio(array &$radio, string $emisoraLwr): bool
    {
        $ahora = time();
        $audio_actual = $radio['audio_actual'] ?? null;
        $arranco_en = $radio['arranco_en'] ?? 0;
        $duracion_actual = $this->convertirASegundos($audio_actual['duracion'] ?? 0);
        $tiempo_transcurrido = $ahora - $arranco_en;
        $tiempo_faltante = $duracion_actual - $tiempo_transcurrido;
        // ponemos los datos en el log
        Log::info("verificarCambioAudio", [
            'audio_actual' => $audio_actual,
            'duracion_actual_segundos' => $duracion_actual,
            'arranco_en' => $arranco_en,
            'ahora' => $ahora,
            'transcurrido' => $tiempo_transcurrido,
            'quedan' => $tiempo_faltante
        ]);

        // Condición para cambiar audio
        if (!$audio_actual || $tiempo_faltante < 0) {
            return $this->seleccionarNuevoAudio($radio, $emisoraLwr);
        }

        return false;
    }

    /**
     * Seleccionar nuevo audio o jingle
     */
    private function seleccionarNuevoAudio(array &$radio, string $emisoraLwr): bool
    {
        // Si hay audio siguiente (típicamente un jingle)
        if ($radio['audio_siguiente'] ?? null) {
            $radio['audio_actual'] = $radio['audio_siguiente'];
            $radio['reproduciendo_jingle'] = false;
            unset($radio['audio_siguiente']);
            $radio['arranco_en'] = time();
            return true;
        }

        // Buscar siguiente audio de la emisora
        $audio_actual = $this->siguienteAudio($emisoraLwr, $radio['audio_actual']['id'] ?? 0);

        if (!$audio_actual) {
            Log::error("No se pudo obtener un nuevo audio de la radio");
            return false;
        }

        $audio_actual = $audio_actual->toArray();
        $jingles = $this->obtenerJingles();

        // Insertar jingle entre audios
        if (count($jingles)) {
            $radio['audio_siguiente'] = $audio_actual;
            $radio['reproduciendo_jingle'] = true;
            $j = ($radio['jingle_idx'] ?? -1 + 1) % count($jingles);
            $radio['audio_actual'] = $jingles[$j];
            $radio['jingle_idx'] = $j;
        } else {
            $radio['audio_actual'] = $audio_actual;
        }

        $radio['arranco_en'] = time();
        return true;
    }

    /**
     * Convertir tiempo a segundos
     */
    private function convertirASegundos($v): int
    {
        if (is_int($v)) return $v;
        if (!preg_match("/:/", $v)) return intval($v);
        $t = explode(':', $v);
        return intval(end($t)) + intval(prev($t)) * 60 + intval(prev($t)) * 3600;
    }

    /**
     * Guardar configuración de radio
     */
    private function guardarConfiguracionRadio(string $nombreConfiguracion, array $radio): void
    {
        $json_radio = json_encode($radio, JSON_PRETTY_PRINT);
        Setting::updateOrCreate(
            ['name' => $nombreConfiguracion],
            ['value' => $json_radio]
        );
    }

    /**
     * Generar estado de radio para respuesta
     */
    private function generarEstadoRadio(array $radio, string $emisora): array
    {
        $ahora = time();

        $duracion_actual = $this->convertirASegundos($radio['audio_actual']['duracion'] ?? 0);

        $posicion_actual = min($ahora - $radio['arranco_en'], $duracion_actual);

        $estado = [
            'emisora' => $emisora,
            'audio_actual' => $radio['audio_actual'],
            'es_jingle' => $radio['reproduciendo_jingle'],
            'duracion_en_segundos' => $duracion_actual,
            'tiempo_sistema' => $ahora,
            'arranco_en' => $radio['arranco_en'] ?? now(),
            'termina_en' => $radio['arranco_en'] + $duracion_actual,
            'posicion_actual' => $posicion_actual,
            'segundos_restantes' =>  $duracion_actual - $posicion_actual,
        ];

        // Añadir audio siguiente si es un jingle
        if ($radio['reproduciendo_jingle']) {
            $estado['audio_siguiente'] = $radio['audio_siguiente'] ?? null;
        }

        return $estado;
    }

    /**
     * Renderizar vista de emisora
     */
    private function renderizarVistaEmisora(array $estado)
    {
        $categorias = $this->obtenerCategoriasEmisoras();

        return Inertia::render('Radio/Emisora', [
            'estado' => $estado,
            'emisoras' => $categorias
        ])->withViewData(SEO::get('radio'));
    }

    /**
     * Obtener categorías de emisoras
     */
    private function obtenerCategoriasEmisoras()
    {
        return RadioItem::selectRaw('distinct categoria')
            ->where('categoria', '<>', 'Jingles')
            ->where('desactivado', '<>', 1)
            ->get()
            ->pluck('categoria');
    }

    /**
     * Obtener jingles disponibles
     */
    private function obtenerJingles(): array
    {
        return RadioItem::where("categoria", 'Jingles')->get()->toArray();
    }

    /**
     * Obtener siguiente audio para una emisora
     */
    private function siguienteAudio($emisora, $id)
    {
        if(!$id||!is_numeric($id)) $id = 0;
        $emisora = strtolower($emisora);

        $sql = RadioItem::whereRaw("(LOWER(categoria)='$emisora' AND desactivado!=1)")
            ->where('id', '>', $id)
            ->orderBy('id', 'asc');

        $audio =  $sql->first();

        if (!$audio && $id) {
            return $this->siguienteAudio($emisora, 0);
        }

        return $audio;
    }
}
