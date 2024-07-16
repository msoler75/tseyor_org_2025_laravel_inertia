<?php


namespace App\Workers;

use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Email;

/**
 * - Crea un lock
 * - Detecta y registra los correos (emails)
 */
class WorkerLock extends Worker
{

    // Override

    public function runNextJob($connectionName, $queue, WorkerOptions $options)
    {
        Log::channel('jobs')->info("WorkerLock.runNextJob ====================================================");

        $lockName = 'worker_lock';
        $lockTimeout = 20; // Tiempo de expiración del bloqueo en segundos

        $lock = Cache::lock($lockName, $lockTimeout);

        // adquiere el lock
        if ($lock->get()) {
            try {

                $job = $this->getNextJob($this->manager->connection($connectionName), $queue);

                if ($job) {

                    $jobId = $job->getJobId();
                    $payload = $job->payload();

                    Log::channel('jobs')->info("Job $jobId " . $payload['displayName']);

                    if ($payload && isset($payload['data']['commandName']) && $payload['data']['commandName'] === 'Illuminate\Mail\SendQueuedMailable') {
                        // Es una instancia de SendQueuedMailable
                        $mailableData = $payload['data']['command'];
                        $mailable = unserialize($mailableData);
                        $object = $mailable->mailable;
                        if ($object) {

                            if (method_exists($object, '__toString')) {
                                Log::channel('jobs')->info($object->__toString());
                            }

                            $data = [
                                "body" => $object->render(),
                                "subject" => $object->subject ?? 'Sin asunto',
                                "from" => $object->from,
                                "to" => $object->to
                            ];

                            $data["from"] = $this->toAddressList($data["from"]);
                            $data["to"] = $this->toAddressList($data["to"]);

                            // Guarda los datos en el modelo Email
                            Email::create($data);
                        }
                    }

                    $result = $this->runJob($job, $connectionName, $options);

                    Log::channel('jobs')->info("job finished");

                    return $result;
                }
            } catch (\Throwable $e) {

                Log::channel('jobs')->error("Exception: ",
                [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            } finally {
                $lock->release();
                $this->sleep(max(1, $options->sleep));
            }
        }
    }


    /**
     * Convierte una lista de direcciones a un string del tipo "María Lopez" <maria.lopez@gmail.com>, "Jorge" <jorge9234@yahoo.es>
     */
    private function toAddressList($field)
    {
        $toEmails = [];
        foreach ($field as $recipient) {
            $name = $recipient['name'];
            $address = $recipient['address'];
            $toEmails[] = $name ? "\"$name\" <$address>" : $address;
        }
        return implode(', ', $toEmails);
    }
}
