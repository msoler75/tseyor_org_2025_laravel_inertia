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
use Jaybizzle\CrawlerDetect\CrawlerDetect;

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
     * Detectar si el request viene de un bot de red social o mensajería
     * NO incluye herramientas SEO, motores de búsqueda ni otros crawlers
     * que necesitan ver el contenido real con metadatos
     */
    protected function esBotRedSocial($request): bool
    {
        $userAgent = $request->header('User-Agent', '');

        // Lista específica de bots de redes sociales y mensajería
        // (NO incluye herramientas SEO, Google, Bing, etc.)
        $redesSocialesYMensajeria = [
            'facebookexternalhit',      // Facebook
            'facebookcatalog',          // Facebook Catalog
            'Facebot',                  // Facebook
            'Twitterbot',               // Twitter
            'LinkedInBot',              // LinkedIn
            'WhatsApp',                 // WhatsApp
            'TelegramBot',              // Telegram
            'Slackbot',                 // Slack
            'Discordbot',               // Discord
            'Pinterest',                // Pinterest
            'Pinterestbot',             // Pinterest
            'instagram',                // Instagram (raro, pero posible)
            'SkypeUriPreview',          // Skype
            'Iframely',                 // iframely (preview service)
            'vkShare',                  // VKontakte
            'redditbot',                // Reddit
            'Tumblr',                   // Tumblr
            'Applebot',                 // Apple (iMessage previews)
            'developers.google.com/+/web/snippet', // Google+ (deprecado pero por si acaso)
        ];

        // Verificar si el user agent contiene alguno de los identificadores
        foreach ($redesSocialesYMensajeria as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
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
        // No reportar ModelNotFoundException ya que se maneja como 404 y se loguea en canal 'notfound'
        if ($exception instanceof ModelNotFoundException) {
            return;
        }

        // Verificar si la excepción está relacionada con el envío de correos
        if ($this->isMailException($exception)) {
            Log::channel('smtp')->error('Mail Exception: ' . $exception->getMessage(), ['exception' => $exception]);
        }

        // Verificar si la excepción está relacionada con la ejecución de jobs
        else if ($this->isJobException($exception)) {
            Log::channel('jobs')->error('Job Exception: ' . $exception->getMessage(), ['exception' => $exception]);
        }
        else {
            // Verificar si es un error 500
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                if ($exception->getStatusCode() >= 500) {
                    Log::channel('500')->error($exception->getStatusCode() . ' Error: ' . $exception->getMessage(), [
                        'url' => request()->getRequestUri(),
                        'exception' => $exception
                    ]);
                }
            } else {
                // Excepciones no HTTP son generalmente 500
                Log::channel('500')->error('500 Error: ' . $exception->getMessage(), [
                    'url' => request()->getRequestUri(),
                    'exception' => $exception
                ]);
            }
            parent::report($exception);
        }
    }


    /**
     * Muestra una página 404 con resultados relevantes
     */

    public function mostrar404($request, Throwable $exception)
    {
        // Si es un bot (cualquier crawler), devolver respuesta simple sin búsqueda de alternativas
        $crawlerDetect = new CrawlerDetect();
        if ($crawlerDetect->isCrawler($request->header('User-Agent'))) {
            try {
                return response()->view('errors.bot', [
                    'codigo' => 404,
                    'mensaje' => 'Contenido no encontrado',
                ], 404);
            } catch (\Exception $viewException) {
                // Fallback si la vista no existe
                return response('Contenido no encontrado', 404);
            }
        }

        try {
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

            $resultados = BusquedasHelper::buscarContenidos($buscar, $coleccion, false);

            if ($resultados->count() == 0)
                $resultados = BusquedasHelper::buscarContenidos($buscar, null, false);

            // si solo hay un resultado, redirigimos automáticamente
            /*if ($resultados->count() == 1) {
                $primerResultado = $resultados->first();
                return redirect()->to($primerResultado->url);
            }*/

            // $message = $exception->getMessage();
            return Inertia::render('Error', [
                'codigo' => 404, //$statusCode,
                'titulo' =>  'Contenido no encontrado',
                'mensaje' => 'No se encuentra el recurso solicitado.',
                'alternativas' => $resultados
            ])->withViewData(['noindex' => true])->toResponse($request);
        } catch (Throwable $e) {
            // Si algo falla en mostrar404, loguear el error y devolver un 404 básico
            Log::error('Error en mostrar404: ' . $e->getMessage(), [
                'original_exception' => $exception->getMessage(),
                'path' => $request->path(),
                'url' => $request->fullUrl(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Contenido no encontrado', 404);
        }
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

        // Si es un bot de red social, devolver respuesta simple sin Inertia/SEO
        if ($this->esBotRedSocial($request)) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;

            // Log detallado para debugging de bots
            Log::warning('Bot detectado en error handler - Evitando Inertia/SEO', [
                'user_agent' => $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'route' => $request->route() ? $request->route()->getName() : 'N/A',
                'route_uri' => $request->route() ? $request->route()->uri() : 'N/A',
                'error_type' => get_class($exception),
                'error_message' => $exception->getMessage(),
                'error_file' => $exception->getFile(),
                'error_line' => $exception->getLine(),
                'status_code' => $statusCode,
            ]);

            // Intentar usar la vista errors.bot, con fallback a HTML inline
            try {
                return response()->view('errors.bot', [
                    'codigo' => $statusCode,
                    'mensaje' => $exception->getMessage(),
                ], $statusCode);
            } catch (\Exception $viewException) {
                // Fallback si la vista no existe (por ejemplo, en deployment incompleto)
                Log::error('Vista errors.bot no encontrada, usando fallback HTML', [
                    'view_error' => $viewException->getMessage()
                ]);

                $appName = config('app.name', 'TSEYOR.org');
                $appUrl = config('app.url', 'https://tseyor.org');

                $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {$statusCode} - {$appName}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{$appName}">
</head>
<body style="font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px;">
        <div style="font-size: 48px; font-weight: bold; color: #999;">{$statusCode}</div>
        <h1 style="color: #d32f2f;">Error en el servidor</h1>
        <p style="color: #666;">El contenido solicitado no está disponible en este momento.</p>
        <p><a href="{$appUrl}" style="color: #1976d2; text-decoration: none;">← Volver al inicio</a></p>
    </div>
</body>
</html>
HTML;

                return response($html, $statusCode)->header('Content-Type', 'text/html');
            }
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
            $referer = $request->header('Referer') ?? '';
            $uri = $request->getRequestUri();

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
            // Información adicional de contexto
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route() ? $request->route()->getName() : 'N/A',
            'route_uri' => $request->route() ? $request->route()->uri() : 'N/A',
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'is_bot' => $this->esBotRedSocial($request),
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
