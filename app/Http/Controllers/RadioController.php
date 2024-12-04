<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Setting;
use App\Models\RadioItem;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

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
    public function emisora(Request $request, string $emisora)
    {
        try {
            DB::beginTransaction();

            $estado = $this->gestionarEstadoRadio($emisora);

            DB::commit();

            return $this->renderizarVistaEmisora($estado);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en reproducción de emisora', [
                'emisora' => $emisora,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Gestionar el estado completo de la radio
     */
    private function gestionarEstadoRadio(string $emisora): array
    {
        // Preparar información de la emisora
        $emisoraLimpia = $this->sanearNombreEmisora($emisora);
        $nombreConfiguracion = 'radio_' . $emisoraLimpia;

        // Obtener o inicializar estado de la radio
        $estadoRadio = $this->obtenerConfiguracionRadio($nombreConfiguracion);

        // Determinar si es necesario cambiar el audio
        $modificado = $this->verificarCambioAudio($estadoRadio, $emisoraLimpia);

        // Guardar configuración si ha cambiado
        if ($modificado) {
            $this->guardarConfiguracionRadio($nombreConfiguracion, $estadoRadio);
        }

        // Preparar estado para respuesta
        return $this->generarEstadoRadio($estadoRadio, $emisora);
    }

    /**
     * Sanear el nombre de la emisora para prevenir inyección
     */
    private function sanearNombreEmisora(string $emisora): string
    {
        return filter_var(strtolower($emisora), FILTER_SANITIZE_ADD_SLASHES);
    }

    /**
     * Obtener o inicializar la configuración de radio con valores por defecto
     */
    private function obtenerConfiguracionRadio(string $nombreConfiguracion): array
    {
        try {
            $configuracion = Setting::where('name', $nombreConfiguracion)->first();
            $estadoRadio = $configuracion ? json_decode($configuracion->value, true) : null;
        } catch (Exception $err) {
            Log::error('Error al decodificar configuración ' . $nombreConfiguracion, [
                'error' => $err->getMessage()
            ]);
            $estadoRadio = null;
        }

        // Estado inicial por defecto
        return $estadoRadio ?: $this->crearEstadoRadioInicial();
    }

    /**
     * Crear estado inicial por defecto para la radio
     */
    private function crearEstadoRadioInicial(): array
    {
        return [
            'reproduciendo_jingle' => false,
            'audio_actual' => null,
            'arranco_en' => now()->timestamp,
            'audio_siguiente' => null,
            'jingle_idx' => null
        ];
    }

  /**
     * Verificar si es necesario cambiar el audio con control de concurrencia
     */
    private function verificarCambioAudio(array &$estadoRadio, string $emisoraLimpia): bool
    {
        $ahora = time();
        $audioActual = $estadoRadio['audio_actual'] ?? null;

        // Si no hay audio actual, definitivamente hay que cambiarlo
        if (!$audioActual) {
            return $this->seleccionarNuevoAudio($estadoRadio, $emisoraLimpia);
        }

        $arranqueEn = $estadoRadio['arranco_en'] ?? 0;
        $duracionActual = $this->convertirASegundos($audioActual['duracion'] ?? 0);
        $tiempoTranscurrido = $ahora - $arranqueEn;
        $tiempoRestante = $duracionActual - $tiempoTranscurrido;

        // Registro detallado de información
        $this->registrarDetallesAudio($audioActual, $duracionActual, $arranqueEn, $ahora, $tiempoTranscurrido, $tiempoRestante);

        // Bloqueo de concurrencia para cambio de audio
        $lockKey = 'radio_cambio_audio_' . $emisoraLimpia;

        // Intentar obtener un bloqueo con tiempo límite
        if (Cache::add($lockKey, true, Carbon::now()->addSeconds(5))) {
            try {
                // Si el tiempo restante es negativo, seleccionar nuevo audio
                if ($tiempoRestante < 0) {
                    return $this->seleccionarNuevoAudio($estadoRadio, $emisoraLimpia);
                }
                return false;
            } finally {
                // Liberar el bloqueo
                Cache::forget($lockKey);
            }
        }

        // Si no se puede obtener el bloqueo, no cambiar el audio
        return false;
    }

     /**
     * Registrar detalles del estado del audio para depuración
     */
    private function registrarDetallesAudio($audioActual, $duracionActual, $arranqueEn, $ahora, $tiempoTranscurrido, $tiempoRestante): void
    {
        Log::info("Verificación de cambio de audio", [
            'audio_actual' => $audioActual,
            'duracion_actual_segundos' => $duracionActual,
            'arranco_en' => $arranqueEn,
            'ahora' => $ahora,
            'transcurrido' => $tiempoTranscurrido,
            'quedan' => $tiempoRestante
        ]);
    }

     /**
     * Seleccionar nuevo audio con protección de carrera
     */
    private function seleccionarNuevoAudio(array &$estadoRadio, string $emisoraLimpia): bool
    {
        // Verificar si ya existe un audio actual
        if ($estadoRadio['audio_actual'] !== null) {
            // Si el audio actual sigue siendo válido, no cambiarlo
            $ahora = time();
            $duracionActual = $this->convertirASegundos($estadoRadio['audio_actual']['duracion'] ?? 0);
            $tiempoTranscurrido = $ahora - $estadoRadio['arranco_en'];

            if ($tiempoTranscurrido < $duracionActual) {
                return false;
            }
        }

        // Si hay audio siguiente (típicamente un jingle)
        if ($estadoRadio['audio_siguiente'] ?? null) {
            $this->transicionarAudioSiguiente($estadoRadio);
            return true;
        }

        // Buscar siguiente audio de la emisora
        $audioActual = $this->siguienteAudio($emisoraLimpia, $estadoRadio['audio_actual']['id'] ?? 0);

        if (!$audioActual) {
            Log::error("No se pudo obtener un nuevo audio de la radio");
            return false;
        }

        $jingles = $this->obtenerJingles();
        $this->insertarJingleSiDisponible($estadoRadio, $audioActual, $jingles);

        $estadoRadio['arranco_en'] = time();
        return true;
    }

    /**
     * Transicionar al audio siguiente (típicamente después de un jingle)
     */
    private function transicionarAudioSiguiente(array &$estadoRadio): void
    {
        $estadoRadio['audio_actual'] = $estadoRadio['audio_siguiente'];
        $estadoRadio['reproduciendo_jingle'] = false;
        unset($estadoRadio['audio_siguiente']);
        $estadoRadio['arranco_en'] = time();
    }


    /**
     * Insertar jingle si está disponible
     */
    private function insertarJingleSiDisponible(array &$estadoRadio, $audioActual, array $jingles): void
    {
        $audioActual = $audioActual->toArray();

        if (count($jingles)) {
            $estadoRadio['audio_siguiente'] = $audioActual;
            $estadoRadio['reproduciendo_jingle'] = true;
            $j = ($estadoRadio['jingle_idx'] ?? -1 + 1) % count($jingles);
            $estadoRadio['audio_actual'] = $jingles[$j];
            $estadoRadio['jingle_idx'] = $j;
        } else {
            $estadoRadio['audio_actual'] = $audioActual;
        }
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

        $audio = RadioItem::whereRaw("(LOWER(categoria)='$emisora' AND desactivado!=1)")
            ->where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        if (!$audio && $id) {
            return $this->siguienteAudio($emisora, 0);
        }

        return $audio;
    }
}
