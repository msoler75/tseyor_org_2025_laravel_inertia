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
        Log::channel('jobs')->info("WorkerLock.runNextJob");

        $lockName = 'worker_lock';
        $lockTimeout = 1; // Tiempo de expiración del bloqueo en segundos

        //$lock = Cache::lock($lockName, $lockTimeout);

        // adquiere el lock
        //if ($lock->get())
        {

            try {

                $job = $this->getNextJob($this->manager->connection($connectionName), $queue);

                if ($job) {

                    $payload = $job->getRawBody();
                    $payloadData = json_decode($payload, true);

                    if ($payloadData && isset($payloadData['data']['commandName']) && $payloadData['data']['commandName'] === 'Illuminate\Mail\SendQueuedMailable') {
                        // Es una instancia de SendQueuedMailable
                        $mailableData = $payloadData['data']['command'];
                        $mailable = unserialize($mailableData);
                        $object = $mailable->mailable;
                        $data = [
                            "body" => $object->render(),
                            "subject" => $object->subject,
                            "from" => $object->from,
                            "to" => $object->to
                        ];

                        $data["from"] = $this->toAddressList($data["from"]);
                        $data["to"] = $this->toAddressList($data["to"]);

                        // Guarda los datos en el modelo Email
                        Email::create($data);
                    }

                    return $this->runJob($job, $connectionName, $options);
                }

            } catch (\Exception $e) {

                Log::channel('jobs')->error($e->getMessage());

            } finally {
                //$lock->release();
                $this->sleep($options->sleep);
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
