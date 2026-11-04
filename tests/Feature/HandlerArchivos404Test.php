<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class HandlerArchivos404Test extends TestCase
{
    /**
     * Renderiza un 404 para /archivos/equipos/interiorizacion directamente
     * desde el handler, con o sin cabecera X-Inertia (navegación Link).
     */
    private function render404(bool $peticionInertia)
    {
        $server = $peticionInertia ? ['HTTP_X_Inertia' => 'true'] : [];
        $request = Request::create('/archivos/equipos/interiorizacion', 'GET', server: $server);
        $exception = new NotFoundHttpException('Ruta no encontrada');

        return app(Handler::class)->render($request, $exception);
    }

    public function test_peticion_inertia_a_archivo_inexistente_devuelve_pagina_error(): void
    {
        $response = $this->render404(true);

        // Debe ser una respuesta Inertia válida: si no, el cliente muestra su modal de error.
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('true', $response->headers->get('X-Inertia'));

        $page = json_decode($response->getContent(), true);
        $this->assertSame('Error', $page['component']);
        $this->assertSame(404, $page['props']['codigo']);
        $this->assertSame('Contenido no encontrado', $page['props']['titulo']);
    }

    public function test_peticion_directa_a_archivo_inexistente_devuelve_404_http(): void
    {
        $response = $this->render404(false);

        // Descargas/carga directa: debe conservar el 404 HTTP real, sin cabecera Inertia.
        $this->assertSame(404, $response->getStatusCode());
        $this->assertNull($response->headers->get('X-Inertia'));
    }
}
