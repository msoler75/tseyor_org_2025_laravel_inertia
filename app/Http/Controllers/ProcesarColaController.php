<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;
use App\Models\Email;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;

class ProcesarColaController extends Controller
{
    public function process()
    {
        $start = time(); // Tiempo de inicio de la ejecución
        $maxExecutionTime = config("queue.max_time_processing_queue", 60); // Tiempo máximo de ejecución en segundos

        while (true) {

            $job = null;
            $pendingTasks = 0;

            // Intenta obtener el bloqueo de exclusión mutua
            $lock = Cache::lock('jobs', 40);

            if ($lock->get()) {

                // Consulta el siguiente trabajo en la cola de base de datos
                $job = DB::table('jobs')->orderBy('id')->first();

                if ($job) {

                    try {
                        // Procesa el trabajo
                        $payload = unserialize($job->payload);
                        dispatch($payload->job);

                        // Extrae los datos básicos del Mailable
                        if ($payload->job instanceof Mailable) {
                            $build = $payload->job->build();
                            dd($build);
                            $mailableData = [
                                'body' => $payload->job->buildView(),
                                'subject' => $build->subject,
                                'from' => $build->from,
                                'to' => $build->to,
                            ];

                            // Guarda los datos en el modelo Email
                            $email = new Email();
                            $email->body = $mailableData['body'];
                            $email->subject = $mailableData['subject'];
                            $email->from = $mailableData['from'];
                            $email->to = $mailableData['to'];
                            $email->save();
                        }


                    } catch (\Exception $e) {
                        Log::error('Error processing job: ' . $e->getMessage());
                    }
                    // Elimina el trabajo de la cola
                    DB::table('jobs')->where('id', $job->id)->delete();

                    // Calcula el número de tareas pendientes
                    $pendingTasks = Queue::size();
                }

                // libera el lock
                $lock->release();

            }
            else {
                // Calcula el número de tareas pendientes
                $pendingTasks = Queue::size();
            }

            // Comprueba si queda poco tiempo de ejecución
            $elapsedTime = time() - $start;
            $remainingTime = $maxExecutionTime - $elapsedTime;

            if (!$pendingTasks || $remainingTime < 30) {
                break; // Detén la ejecución si queda poco tiempo
            }
        }

        return response()->json(['pending_tasks' => $pendingTasks]);
    }
}
