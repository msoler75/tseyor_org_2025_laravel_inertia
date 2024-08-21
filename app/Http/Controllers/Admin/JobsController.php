<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Comunicado;
use App\Models\Informe;
use App\Jobs\ProcesarAudios;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Log;
use App\Workers\WorkerLock;


class JobsController extends Controller
{
    /**
     * Reencola todas las tareas fallidas
     * @return \Illuminate\Http\Response
     */
    public function retryFailedJobs()
    {
        Artisan::call('queue:retry all');
        Alert::add('success', 'Todas las tareas han sido encoladas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Reencola una tarea fallida
     * @return \Illuminate\Http\Response
     */
    public function retryJob($id)
    {
        Artisan::call('queue:retry ' . $id);
        Alert::add('success', 'La tarea $id ha sido encoladas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Elimina tareas fallidas
     * @return \Illuminate\Http\Response
     */
    public function flushJobs() {
        Artisan::call('queue:flush');
        Alert::add('success', 'Tareas eliminadas')->flash();
        return redirect()->to('/admin/job-failed');
    }


    /**
     * Detecta contenidos con audios a procesar y crea las jobs
     * @return \Illuminate\Http\Response
     */
    public function detectAudiosToProcess() {
        $tareas = 0;
        // SELECT id, titulo, audios FROM `comunicados` WHERE audios LIKE '%upload%';
        $comunicados = Comunicado::select(['id', 'fecha_comunicado'])->where('audios', 'LIKE', '%upload%')->get();
        foreach($comunicados as $comunicado) {
            $año = date('Y', strtotime($comunicado->fecha_comunicado));
            $folder = "/almacen/medios/comunicados/audios/$año";
            dispatch(new ProcesarAudios(Comunicado::class, $comunicado->id, $folder))->onQueue('audio_processing');
            $tareas++;
        }

        $informes = Informe::where('audios', 'LIKE', '%upload%')->with('equipo')->get();
        foreach($informes as $informe) {
            $año = $informe->created_at->year;
            $folder = "/almacen/medios/informes/audios/{$informe->equipo->slug}/$año/{$informe->id}";
            dispatch(new ProcesarAudios(Informe::class, $informe->id, $folder))->onQueue('audio_processing');
            $tareas++;
        }

        Alert::add('success', "Se han agregado $tareas tareas")->flash();
        return redirect()->to('/admin/job');
    }




    /**
     * Ejecuta un worker para procesar la cola de audios
     */
    public function processAudios()
    {
        Log::channel('jobs')->info("WorkerController.process");

        $start = time(); // Tiempo de inicio de la ejecución
        $maxExecutionTime = config("queue.max_time_processing_queue", 60); // Tiempo máximo de ejecución en segundos

        // cola por defecto
        $queue = 'audio_processing';

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
