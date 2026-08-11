<?php

namespace Tests\Feature\MCP;

use App\MCP\Tools\BuscarTool;
use App\MCP\Tools\CrearTool;
use App\MCP\Tools\EditarTool;
use App\MCP\Tools\EliminarTool;
use App\MCP\Tools\InfoTool;
use App\MCP\Tools\ListarTool;
use App\MCP\Tools\VerTool;
use App\Models\Audio;
use App\Models\Centro;
use App\Models\Comunicado;
use App\Models\Contacto;
use App\Models\Entrada;
use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Guia;
use App\Models\Informe;
use App\Models\Libro;
use App\Models\Lugar;
use App\Models\Meditacion;
use App\Models\Normativa;
use App\Models\Noticia;
use App\Models\Pagina;
use App\Models\Psicografia;
use App\Models\Sala;
use App\Models\Termino;
use App\Models\Tutorial;
use App\Models\User;
use App\Models\Video;
use Laravel\Scout\EngineManager;
use Laravel\Scout\ModelObserver;
use Tests\TestCase;

abstract class McpFeatureTestCase extends TestCase
{
    /**
     * Mapa de nombres de tool MCP a sus clases de implementación.
     */
    protected array $toolClasses = [
        'listar' => ListarTool::class,
        'ver' => VerTool::class,
        'buscar' => BuscarTool::class,
        'crear' => CrearTool::class,
        'editar' => EditarTool::class,
        'eliminar' => EliminarTool::class,
        'info' => InfoTool::class,
    ];

    /**
     * Invoca una tool MCP en proceso (sin HTTP ni cURL).
     *
     * Replica el contrato del MCPController HTTP: captura excepciones y las
     * devuelve como array con clave 'error' (status 500 en el flujo real),
     * en vez de propagarlas. Así los tests negativos (p.ej. checkDeleteable
     * de Equipo/Grupo/Informe) se comportan igual que contra el servidor real.
     *
     * @return array
     */
    protected function callMcpTool(string $tool, array $arguments = [])
    {
        $class = $this->toolClasses[$tool] ?? null;

        if ($class === null) {
            return ['error' => "Tool MCP desconocida: {$tool}"];
        }

        try {
            return (new $class)->handle($arguments);
        } catch (\Throwable $e) {
            // Devolver el mensaje real de negocio (p.ej. checkDeleteable) como
            // error, igual que el flujo HTTP expone el mensaje de la excepción.
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // TNTSearch usa índices en filesystem que NO son transaccionales y se
        // contaminan con datos de la DB real o corridas anteriores. Para que las
        // búsquedas sean deterministas:
        //  1) borramos los archivos .index del storage de testing,
        //  2) reseteamos el engine de Scout para que el próximo acceso cree un
        //     TNTSearch fresco (si no, el singleton del proceso PHPUnit conserva
        //     en memoria los índices de tests anteriores), y
        //  3) DESACTIVAMOS el sync automático: indexar en cada create() es
        //     lentísimo (tokenizer Spanish + hooks del modelo). Los tests que
        //     necesitan búsqueda reindexan explícitamente con makeAllSearchable().
        $tntDir = config('scout.tntsearch.storage');
        if (is_dir($tntDir)) {
            foreach (glob($tntDir.'/*.index') ?: [] as $file) {
                @unlink($file);
            }
        }
        app(EngineManager::class)->forgetEngines();
        $this->disableSearchSyncingForAllSearchableModels();
    }

    /**
     * Desactiva el syncing de Scout (TNTSearch) para todos los modelos Searchable.
     *
     * Sin esto, cada Model::create() en un test dispara una indexación TNT
     * (tokenizer Spanish + stemming + fuzziness) que encarece la suite
     * (52 creates = ~22s). Los tests que verifiquen búsquedas deben llamar
     * explícitamente a Model::makeAllSearchable() antes de buscar.
     */
    private function disableSearchSyncingForAllSearchableModels(): void
    {
        $searchable = [
            Audio::class,
            Centro::class,
            Comunicado::class,
            Contacto::class,
            Entrada::class,
            Equipo::class,
            Evento::class,
            Guia::class,
            Informe::class,
            Libro::class,
            Lugar::class,
            Meditacion::class,
            Normativa::class,
            Noticia::class,
            Pagina::class,
            Psicografia::class,
            Sala::class,
            Termino::class,
            Tutorial::class,
            User::class,
            Video::class,
        ];
        foreach ($searchable as $class) {
            ModelObserver::disableSyncingFor($class);
        }
    }

    /**
     * Reindexa todos los registros de un modelo Searchable en el índice TNT.
     * Usar SOLO en tests que verifican búsquedas, justo antes del callMcpTool.
     */
    protected function makeAllSearchable(string $modelClass): void
    {
        $modelClass::makeAllSearchable();
    }
}
