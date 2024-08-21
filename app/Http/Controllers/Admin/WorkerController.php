<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Process;

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



}
