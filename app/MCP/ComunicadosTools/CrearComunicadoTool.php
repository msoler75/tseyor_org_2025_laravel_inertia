<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseTool;

class CrearComunicadoTool extends BaseTool {
    protected string $name = 'crear_comunicado';

    // Métodos DRY heredados de BaseTool
    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        // Validación de permisos y procesamiento de imágenes
        if (isset($params['texto'])) {
            $carpeta = (new Comunicado())->getCarpetaMedios();
            if ($carpeta) {
                $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
            }
        }
        try {
            $comunicado = Comunicado::create($params);
            return $comunicado ? ['comunicado_creado'=>$comunicado->toArray()] : [];
        }
        catch (\Exception $e) {
            return ['error' => 'Error al crear el comunicado: ' . $e->getMessage()];
        }
    }
}
