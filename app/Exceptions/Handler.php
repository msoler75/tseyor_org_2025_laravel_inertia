<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Pigmalion\BusquedasHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

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
     * Determine if the exception is related to mail sending.
     *
     * @param  \Throwable  $exception
     * @return bool
     */
    protected function isMailException(Throwable $exception)
    {
        // Aquí puedes personalizar la lógica para identificar excepciones de correo
        return strpos($exception->getMessage(), 'SMTP') !== false
            || strpos($exception->getMessage(), 'mail') !== false
            || strpos(get_class($exception), 'Mail') !== false
            || $exception instanceof TransportException;
    }

    /**
     * Determine if the exception is related to job execution.
     *
     * @param  \Throwable  $exception
     * @return bool
     */
    protected function isJobException(Throwable $exception)
    {
        // Aquí puedes personalizar la lógica para identificar excepciones relacionadas con jobs
        return strpos($exception->getMessage(), 'Job') !== false
            || strpos($exception->getMessage(), 'Queue') !== false
            || strpos(get_class($exception), 'Job') !== false
            || strpos(get_class($exception), 'Queue') !== false;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        // Verificar si la excepción está relacionada con el envío de correos
        if ($this->isMailException($exception)) {
            Log::channel('smtp')->error('Mail Exception: ' . $exception->getMessage(), ['exception' => $exception]);
        }

        // Verificar si la excepción está relacionada con la ejecución de jobs
        else if ($this->isJobException($exception)) {
            Log::channel('jobs')->error('Job Exception: ' . $exception->getMessage(), ['exception' => $exception]);
        }
        else
        parent::report($exception);
    }


    /**
     * Muestra una página 404 con resultados relevantes
     */

    public function mostrar404($request, Throwable $exception)
    {
        // to-do: obtener path de la ruta actual y redirigir a la vista de error
        $path = $request->path();
        $parts = explode("/", $path);

        $coleccion = null;
        if (count($parts) > 1)
            $coleccion = $parts[0];

        $colecciones_404 = ['nodos', 'archivos', 'almacen'];
        if (in_array($coleccion, $colecciones_404))
            return parent::render($request, $exception);

        $buscar = preg_replace("/[\?\/\.\-]/", " ", urldecode($parts[count($parts) - 1])); // quitar caracteres no permitidos en $path

        $resultados = BusquedasHelper::buscarContenidos($buscar, $coleccion);
        if ($resultados->count() == 0)
            $resultados = BusquedasHelper::buscarContenidos($buscar);

        // si solo hay un resultado, redirigimos automáticamente
        if ($resultados->count() == 1) {
            $primerResultado = $resultados->first();
            return redirect()->to($primerResultado->url);
        }

        // $message = $exception->getMessage();
        return Inertia::render('Error', [
            'codigo' => 404, //$statusCode,
            'titulo' =>  'Contenido no encontrado',
            'mensaje' => 'No se encuentra el recurso solicitado.',
            'alternativas' => $resultados
        ])->toResponse($request);
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

        if ($exception->getMessage() == 'Service Unavailable') {
            return response()->view('mantenimiento', [], 503);
        }

        // Manejo específico para excepciones de rate limiting (429)
        if ($exception instanceof TooManyRequestsHttpException) {
            $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Demasiadas peticiones. Por favor, espera un momento antes de intentar de nuevo.',
                    'retry_after' => $retryAfter
                ], 429);
            }

            return Inertia::render('Error', [
                'codigo' => 429,
                'titulo' => 'Demasiadas peticiones',
                'mensaje' => 'Has realizado demasiadas peticiones en muy poco tiempo. Por favor, espera un momento antes de continuar.',
                'retry_after' => $retryAfter,
                'alternativas' => collect([])
            ])->toResponse($request);
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {

            Log::channel('notfound')->info($request->fullUrl());

            // si se carga desde otra página (por ejemplo, una imagen) entonces simplemente devolvemos el error
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $uri = $_SERVER['REQUEST_URI'] ?? '';

            // si es una imagen cargada desde una página, devuelve 404 normalmente
            if (
                preg_match("/\.(png|svg|jpg|jpeg|gif|webp|svg)(\?.*)?/", $uri)
                && $referer
                && strpos($referer, $uri) === FALSE
            ) // comprueba si referer es la misma url de la request
                return parent::render($request, $exception);


            return $this->mostrar404($request, $exception);
        }

        // en algunos casos hemos de pasar "arriba" la excepción para que sea gestionada como corresponde

        // ValidationException: login
        if ($exception instanceof ValidationException) {
            $reserved_keys = $this->dontFlash;
            $filteredParams = collect($request->post())->map(function ($value, $key) use ($reserved_keys) {
                return in_array($key, $reserved_keys) ? '********' : $value;
            })->toArray();

            Log::channel('validation')->info($exception->getMessage(), [
                'params' => $filteredParams,
                'url' => $request->fullUrl(),
            ]);
            return parent::render($request, $exception);
        }

        if ($exception instanceof AuthenticationException) {

            Log::channel('validation')->info($exception->getMessage(), [
                'url' => $request->fullUrl(),
            ]);
            return $request->expectsJson()
                ? response()->json(['message' => 'Debes iniciar sesión.'], 401)
                : redirect()->guest(route('login'));
        }

        if($this->isMailException($exception))
        {
            return parent::render($request, $exception);
        }

        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 512;

        Log::error('An exception occurred', [
            'statusCode' => $statusCode,
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);


        if ($exception instanceof InvalidSignatureException) {
            return Inertia::render('Error', [
                'codigo' => $statusCode,
                'titulo' => 'Enlace no válido o caducado',
                'mensaje' => '',
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
