<?php
namespace App\MCP\EntradasTools;

use App\Models\Entrada;
use App\MCP\BaseTool;

class CrearEntradaTool extends BaseTool {
    protected string $name = 'crear_entrada';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        // Procesar imágenes en el texto antes de crear
        if (isset($params['texto'])) {
            $carpeta = (new Entrada)->getCarpetaMedios();
            $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
        }
        try {
            $entrada = Entrada::create($params);
            return $entrada ? ['entrada_creada'=>$entrada->toArray()] : [];
        }
        catch (\Exception $e) {
            return ['error' => 'Error al crear la entrada: ' . $e->getMessage()];
        }
    }
}
