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
        $data = $params['request'] ?? $params;
        if (isset($data['texto'])) {
            $carpeta = method_exists(Noticia::class, 'getCarpetaMedios') ? (new \App\Models\Noticia)->getCarpetaMedios() : null;
            Log::channel('mcp')->info('Carpeta de medios', ['carpeta' => $carpeta]);
            if ($carpeta) {
                $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
                Log::channel('mcp')->info('Texto procesado por extraerImagenes', ['texto' => $data['texto']]);
            }
        }
        try {
            Log::channel('mcp')->info('Creando noticia con datos', ['params' => $data]);
            $noticia = Noticia::create($data);
            Log::channel('mcp')->info('Noticia creada', ['noticia' => $noticia]);
            return $noticia ? ['noticia_creada'=>$noticia->toArray()] : [];
        }
        catch (\Exception $e) {
            Log::channel('mcp')->error('Error al crear la noticia', ['exception' => $e]);
            return ['error' => 'Error al crear la noticia: ' . $e->getMessage()];
        }
    }
}
