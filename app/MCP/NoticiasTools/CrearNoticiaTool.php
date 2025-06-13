<?php
namespace App\MCP\NoticiasTools;

use App\Models\Noticia;
use App\MCP\BaseTool;

class CrearNoticiaTool extends BaseTool {
    protected string $name = 'crear_noticia';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        if (isset($params['texto'])) {
            $carpeta = method_exists(\App\Models\Noticia::class, 'getCarpetaMedios') ? (new \App\Models\Noticia)->getCarpetaMedios() : null;
            if ($carpeta) {
                $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
            }
        }
        try {
            $noticia = \App\Models\Noticia::create($params);
            return $noticia ? ['noticia_creada'=>$noticia->toArray()] : [];
        }
        catch (\Exception $e) {
            return ['error' => 'Error al crear la noticia: ' . $e->getMessage()];
        }
    }
}
