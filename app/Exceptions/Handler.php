<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Pigmalion\BusquedasHelper;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Guardar la excepción en el log con información detallada
        // Log::error($exception->getMessage(), ['exception' => $exception]);

       $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 512;

        Log::error('An exception occurred', [
            'statusCode' => $statusCode,
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);



        // en algunos casos hemos de pasar "arriba" la excepción para que sea gestionada como corresponde
        // ValidationException: login
        if ($exception instanceof ValidationException) {
            return parent::render($request, $exception);
        }


        if ($exception instanceof NotFoundHttpException) {

            // si se carga desde otra página (por ejemplo, una imagen) entonces simplemente devolvemos el error
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $uri = $_SERVER['REQUEST_URI'] ?? '';

            if($referer && strpos($referer, $uri)===FALSE) // comprueba si referer es la misma url de la request
                return parent::render($request, $exception);



            // to-do: obtener path de la ruta actual y redirigir a la vista de error
            $path = $request->path();
            $buscar = preg_replace("/[\?\/\.]/", " ", $path); // quitar caracteres no permitidos en $path

            $resultados = BusquedasHelper::buscarContenidos(urldecode($buscar));

            $message = $exception->getMessage();
            return Inertia::render('Error', [
                'codigo' => $statusCode,
                'titulo' =>  'Contenido no encontrado',
                'mensaje' => 'No se encuentra el recurso solicitado.',
                'alternativas' => $resultados
            ])->toResponse($request);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return Inertia::render('Error', [
                'codigo' => $statusCode,
                'titulo' => 'Acceso denegado',
                'mensaje' => $exception->getMessage(),
            ])->toResponse($request);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return Inertia::render('Error', [
                'codigo' => $statusCode,
                'titulo' => 'Acceso no permitido',
                'mensaje' => $exception->getMessage(),
            ])->toResponse($request);
        }

        // Verificar si estamos en modo desarrollo
        if (config('app.debug')) {
            // Devolver la renderización de la excepción por defecto
            return parent::render($request, $exception);
            //return $this->prepareJsonResponse($request, $exception);
        }

        // si no, mostrarmos vista de error al usaurio
        if ($exception instanceof QueryException) {
            if ($exception->getCode() === 2008) {
                // Manejar el error de falta de memoria de MySQL
                // Puedes realizar acciones como mostrar una página de error personalizada, registrar el error, etc.
                return Inertia::render('Error', [
                    'codigo' => 503,
                    'titulo' => 'Servidor no disponible',
                    'mensaje' => 'Actualmente estamos experimentando problemas técnicos. Por favor, intente nuevamente más tarde.',
                ])->toResponse($request);
            } else {
                return Inertia::render('Error', [
                    'codigo' => 500,
                    'titulo' => 'Error en la aplicación',
                    'mensaje' => 'Ocurrió un error. Por favor, intente nuevamente más tarde.',
                ])->toResponse($request);
            }
        }

        return Inertia::render('Error', [
            'codigo' => $statusCode,
            'titulo' => 'Error inesperado',
            'mensaje' => $exception->getMessage(),
        ])->toResponse($request);
    }
}
