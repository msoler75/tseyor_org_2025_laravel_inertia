@extends(backpack_view('blank'))

@section('content')
<style>
    .swal-modal {
        width: auto !important;
    }
    @media (min-width: 720px) {
        .swal-modal {
            min-width: 720px !important;
        }
    }
</style>

<h1>Comandos ARTISAN</h1>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <button command="optimize" class="btn btn-success">optimize</button>
        <button command="cache:clear" class="btn btn-success">cache:clear</button>
        <button command="view:clear" class="btn btn-success">view:clear</button>
        <button command="imagecache:clear" class="btn btn-success">imagecache:clear</button>
        <button command="migrate" class="btn btn-success">migrate</button>
        <button command="sitemap:generate" class="btn btn-success">sitemap:generate</button>
        <button command="auth:clear-resets" class="btn btn-success">auth:clear-resets</button>
        <button command="db:backup" class="btn btn-success">db:backup</button>
        <button command="down" class="btn btn-success">Activar modo mantenimiento</button>
        <button command="up" class="btn btn-success">Desactivar modo mantenimiento</button>
        <button command="config:clear" class="btn btn-success">config:clear</button>
        <button command="route:clear" class="btn btn-success">route:clear</button>
        <button command="storage:link" class="btn btn-success">storage:link</button>
        <button command="migrate:status" class="btn btn-success">migrate:status</button>
        <button command="queue:clear" class="btn btn-success">queue:clear</button>
        <button command="ps aux" class="btn btn-info">Ver procesos del sistema</button>
        <button command="worker:stop" class="btn btn-danger">Detener worker</button>
        <button command="inertia:stop-ssr" class="btn btn-danger">Detener servidor SSR</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('button[command]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    let command = encodeURIComponent(this.getAttribute('command'));

                    // Deshabilitar el botón mientras se ejecuta el comando
                    this.disabled = true;
                    let originalText = this.innerHTML;
                    this.innerHTML = 'Ejecutando...';

                    fetch('/admin/command', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ command: command })
                    })
                    .then(response => {
                        // Manejar respuestas no exitosas
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw { response: { status: response.status, data: data } };
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta del comando:', data);

                        // Mostrar el resultado
                        let output = data.output || data.status || 'Comando ejecutado correctamente';
                        let status = data.exitCode === 0 ? 'success' : 'warning';

                        swal('Comando ejecutado', output, status);
                    })
                    .catch(error => {
                        console.error('Error al ejecutar comando:', error);

                        let errorMessage = 'Error desconocido';
                        let alertType = 'error';

                        if (error.response) {
                            // El servidor respondió con un código de error
                            switch (error.response.status) {
                                case 403:
                                    errorMessage = 'Acceso denegado. Verifica que tengas permisos para ejecutar este comando.';
                                    break;
                                case 404:
                                    errorMessage = 'El comando solicitado no está disponible en este servidor.';
                                    break;
                                case 429:
                                    errorMessage = 'Demasiadas peticiones. Espera un momento antes de ejecutar otro comando.';
                                    alertType = 'warning';
                                    break;
                                case 503:
                                    // Manejar diferentes tipos de errores 503 según el mensaje
                                    if (error.response.data && error.response.data.output) {
                                        errorMessage = error.response.data.output;

                                        // Determinar el tipo de error específico
                                        if (error.response.data.error === 'Error de memoria') {
                                            alertType = 'warning';
                                        } else if (error.response.data.error === 'Error de procesos') {
                                            alertType = 'warning';
                                        } else {
                                            alertType = 'warning';
                                        }
                                    } else {
                                        errorMessage = 'El servidor no tiene recursos suficientes en este momento. Intenta de nuevo en unos segundos.';
                                        alertType = 'warning';
                                    }
                                    break;
                                case 500:
                                    if (error.response.data) {
                                        errorMessage = error.response.data.output ||
                                                     error.response.data.message ||
                                                     error.response.data.error ||
                                                     'Error interno del servidor';
                                    } else {
                                        errorMessage = 'Error interno del servidor';
                                    }
                                    break;
                                default:
                                    if (error.response.data) {
                                        errorMessage = error.response.data.message ||
                                                     error.response.data.error ||
                                                     error.response.data.output ||
                                                     `Error del servidor (${error.response.status})`;
                                    }
                            }
                        } else if (error.message) {
                            // Error de red o en la configuración de la petición
                            errorMessage = error.message.includes('fetch') ?
                                          'No se pudo conectar con el servidor' :
                                          error.message;
                        }

                        swal('Error', errorMessage, alertType);
                    })
                    .finally(() => {
                        // Rehabilitar el botón
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
                });
            });
        });
    </script>
@endsection
