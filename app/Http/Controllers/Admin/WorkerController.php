<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use App\Workers\WorkerLock;

class WorkerController
{

    protected $scriptPath = "";


    public function __construct()
    {
        $this->scriptPath = base_path('bash/');
    }



    public function checkWorkerStatus()
    {
        $result = Process::run("bash {$this->scriptPath}check-worker.sh");

        if ($result->successful()) {
            $output = trim($result->output());
            if ($output === "0") {
                return response()->json(['status' => 'stopped']);
            } else {
                return response()->json(['status' => 'running', 'pid' => $output]);
            }
        } else {
            return response()->json(['error' => 'error'], 500);
        }
    }


    public function startWorker()
    {
        // Ejecutar el script en segundo plano
        $command = "bash {$this->scriptPath}start-worker.sh > /dev/null 2>&1 &";
        $result = Process::run($command);
    
        if ($result->successful()) {
            return response()->json(['status' => 'Worker started']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to start worker'], 500);
        }
    }

    public function stopWorker()
    {
        $result = Process::run("bash {$this->scriptPath}stop-worker.sh");

        if ($result->successful()) {
            return response()->json(['output' => $result->output(), 'status' => 'Worker stopped']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to stop worker'], 500);
        }
    }

    public function restartWorker()
    {
        // Ejecutar el script en segundo plano
        $command = "bash {$this->scriptPath}restart-worker.sh > /dev/null 2>&1 &";
        $result = Process::run($command);

        if ($result->successful()) {
            return response()->json(['output' => $result->output(), 'status' => 'Worker restarted']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to restart worker'], 500);
        }
    }



    /**
     * Ejecuta un worker para procesar la cola
     */
    public function process()
    {
        Log::channel('jobs')->info("WorkerController.process");

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
        $worker = new WorkerLock($manager, $events, $exceptions, $isDownForMaintenance, $resetScope);

        while (true) {

            // Obtiene el siguiente job de la cola
            $worker->runNextJob(null, $queue, $options);

            // Calcula el número de tareas pendientes
            $pendingTasks = Queue::size($queue);

            // Comprueba si queda poco tiempo de ejecución
            $elapsedTime = time() - $start;
            $remainingTime = $maxExecutionTime - $elapsedTime;

            if (!$pendingTasks || $remainingTime < config("queue.min_time_job", 20)) {
                break; // Detén la ejecución si queda poco tiempo
            }
        }

        return response()->json(['pending_tasks' => $pendingTasks]);
    }
}
