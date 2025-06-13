<?php
namespace App\MCP\NoticiasTools;

use App\Models\Noticia;
use App\MCP\BaseTool;
use Illuminate\Support\Facades\Log;

class CrearNoticiaTool extends BaseTool {
    protected string $name = 'crear_noticia';

    public function handle($params) {
        Log::channel('mcp')->info('CrearNoticiaTool handle iniciado', ['params' => $params]);
        $this->checkMcpToken($params, ['administrar_contenidos']);
        if (isset($params['texto'])) {
            $carpeta = method_exists(Noticia::class, 'getCarpetaMedios') ? (new \App\Models\Noticia)->getCarpetaMedios() : null;
            Log::channel('mcp')->info('Carpeta de medios', ['carpeta' => $carpeta]);
            if ($carpeta) {
                $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
                Log::channel('mcp')->info('Texto procesado por extraerImagenes', ['texto' => $params['texto']]);
            }
        }
        try {
            $noticia = Noticia::create($params);
            Log::channel('mcp')->info('Noticia creada', ['noticia' => $noticia]);
            return $noticia ? ['noticia_creada'=>$noticia->toArray()] : [];
        }
        catch (\Exception $e) {
            Log::channel('mcp')->error('Error al crear la noticia', ['exception' => $e]);
            return ['error' => 'Error al crear la noticia: ' . $e->getMessage()];
        }
    }
}
