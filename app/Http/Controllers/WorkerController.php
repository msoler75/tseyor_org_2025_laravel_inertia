<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\WorkerOptions;
use App\Workers\WorkerEmails;

class WorkerController extends Controller
{

    /**
     * Ejecuta un worker para procesar la cola
     */
    public function process()
    {
        $start = time(); // Tiempo de inicio de la ejecución
        $maxExecutionTime = config("queue.max_time_processing_queue", 60); // Tiempo máximo de ejecución en segundos

        // cola por defecto
        $queue = 'default';

        // Configura las opciones del worker
        $options = new WorkerOptions();
        $options->maxTries = 3; // Número máximo de intentos antes de marcar el trabajo como fallido
        $options->sleep = 1; // Tiempo de espera entre los trabajos de la cola

        // Obtiene una instancia del QueueManager
        $manager = Queue::getFacadeRoot();

        // Crea instancias de Dispatcher y ExceptionHandler
        $events = app(\Illuminate\Contracts\Events\Dispatcher::class);
        $exceptions = app(\Illuminate\Contracts\Debug\ExceptionHandler::class);

        // Crea el callable para determinar si la aplicación está en modo de mantenimiento
        $isDownForMaintenance = function () {
            return app()->isDownForMaintenance();
        };

        // Crea el callable opcional para restablecer el alcance antes de procesar cada trabajo
        $resetScope = null; // Puedes proporcionar un callable aquí si es necesario

        // Crea una instancia del Worker con los parámetros necesarios
        $worker = new WorkerEmails($manager, $events, $exceptions, $isDownForMaintenance, $resetScope);

        while (true) {

            // Obtiene el siguiente job de la cola
            $worker->runNextJob(null, $queue, $options);

            // Calcula el número de tareas pendientes
            $pendingTasks = Queue::size($queue);

            // Comprueba si queda poco tiempo de ejecución
            $elapsedTime = time() - $start;
            $remainingTime = $maxExecutionTime - $elapsedTime;

            if (!$pendingTasks || $remainingTime < 15) {
                break; // Detén la ejecución si queda poco tiempo
            }
        }

        return response()->json(['pending_tasks' => $pendingTasks]);
    }
}
