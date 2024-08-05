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
        $lockTimeout = 30; // Tiempo de expiración del bloqueo en segundos

        // adquiere el lock
        $lock = Cache::lock($lockName, $lockTimeout);
        if ($lock->get()) {
            //if (true) {
            try {

                $job = $this->getNextJob($this->manager->connection($connectionName), $queue);

                if ($job) {

                    $jobId = $job->getJobId();
                    $payload = $job->payload();

                    Log::channel('jobs')->info("Job $jobId " . $payload['displayName']);
                    Log::channel('jobs')->info("Job $jobId ", $payload);

                    // si es una notificación:

                    // {"uuid":"025977b2-db9b-4faf-8e35-ba030bde733b","displayName":"App\\Notifications\\CambioPassword","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\Notifications\\SendQueuedNotifications","command":"O:48:\"Illuminate\\Notifications\\SendQueuedNotifications\":3:{s:11:\"notifiables\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:15:\"App\\Models\\User\";s:2:\"id\";a:1:{i:0;i:23;}s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}s:12:\"notification\";O:32:\"App\\Notifications\\CambioPassword\":3:{s:41:\"\u0000App\\Notifications\\CambioPassword\u0000usuario\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":5:{s:5:\"class\";s:15:\"App\\Models\\User\";s:2:\"id\";i:23;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";s:15:\"collectionClass\";N;}s:42:\"\u0000App\\Notifications\\CambioPassword\u0000password\";s:15:\"proteccion.4074\";s:2:\"id\";s:36:\"8fc3ff19-75ad-445f-970e-242a84a01fd0\";}s:8:\"channels\";a:1:{i:0;s:4:\"mail\";}}"}} 
                    // https://laravel.com/docs/queues#notifications
                    // https://laravel.com/docs/queues#sending-notifications
                    /*if (strpos($payload['displayName'], "Notifications") !== FALSE) {
                        $mailableData = $payload['data']['command'];
                        $mailable = unserialize($mailableData);
                        dd($mailable);
                        //$object = $mailable->mailable;
                        dd($payload['data']['command'], $payload);

                        if (method_exists($object, '__toString')) {
                            Log::channel('jobs')->info($object->__toString());
                        }
                    }*/


                    if (
                        $payload && isset($payload['data']['commandName']) && (
                            $payload['data']['commandName'] === 'Illuminate\Mail\SendQueuedMailable'                        ||
                            $payload['data']['commandName'] === 'Illuminate\Notifications\SendQueuedNotifications')
                    ) {
                        // Es una instancia de SendQueuedMailable
                        $mailableData = $payload['data']['command'];
                        $mailable = unserialize($mailableData);
                        $object = $mailable->mailable ?? $mailable->notification;
                        $notifiables =  $mailable->notifiables ?? [];
                        // dd($notifiables, $mailable, $object);

                        if ($object) {

                            if (method_exists($object, '__toString')) {
                                Log::channel('jobs')->info($object->__toString());
                            }

                            $subject = $object->subject ?? 'Notificación';
                            $to = $object->to  ?? null;
                            if ($notifiables) {
                                $subject = str_replace('App\\Notifications\\', '', $payload['displayName']);
                                $user = $notifiables->first();
                                $to = $user->name . ' <' . $user->email . '>';
                            }

                            if (method_exists($object, 'render')) {
                                $body = $object->render();
                            } else if (method_exists($object, 'toMail') && $notifiables) {
                                // renderizar notificación                                
                                $body = $object->toMail($notifiables->first())->render();
                            }

                            $data = [
                                "body" => $body,
                                "subject" => $subject,
                                "from" => $object->from ?? 'Sistema',
                                "to" => $to
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

                Log::channel('jobs')->error(
                    "Exception: ",
                    [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]
                );
            } finally {
                $lock->release();
                $this->sleep(max(1, $options->sleep));
                //$this->sleep(1);
            }
        }
    }


    /**
     * Convierte una lista de direcciones a un string del tipo "María Lopez" <maria.lopez@gmail.com>, "Jorge" <jorge9234@yahoo.es>
     */
    private function toAddressList($field)
    {
        if (is_string($field)) return $field;
        if (!is_array($field)) return $field;
        $toEmails = [];
        foreach ($field as $recipient) {
            $name = $recipient['name'];
            $address = $recipient['address'];
            $toEmails[] = $name ? "\"$name\" <$address>" : $address;
        }
        return implode(', ', $toEmails);
    }
}
