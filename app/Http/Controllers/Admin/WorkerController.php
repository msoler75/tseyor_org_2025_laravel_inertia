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

    private function runDeployCommand($command) {
        $deploy_user = config('app.deploy_user');
        // Pasar la variable de entorno DEPLOY_USER directamente en el comando
        $result = Process::run("DEPLOY_USER={$deploy_user} {$command}");
        return $result;
    }


    public function checkWorkerStatus()
    {
        try {

            $result = $this->runDeployCommand("bash {$this->scriptPath}worker-check.sh");

            if ($result->successful()) {
                $output = trim($result->output());
                if ($output === "0") {
                    return response()->json(['status' => 'stopped']);
                } else {
                    return response()->json(['status' => 'running', 'pid' => $output]);
                }
            } else {
                return response()->json([
                    'error' => 'error',
                    'details' => $result->errorOutput(),
                    'output' => $result->output()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function startWorker()
    {
        // Ejecutar el script en segundo plano
        $command = "bash {$this->scriptPath}worker-start.sh > /dev/null 2>&1 &";
        $result = $this->runDeployCommand($command);

        if ($result->successful()) {
            return response()->json(['status' => 'Worker started']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to start worker'], 500);
        }
    }

    public function stopWorker()
    {
        $result = $this->runDeployCommand("bash {$this->scriptPath}worker-stop.sh");

        if ($result->successful()) {
            return response()->json(['output' => $result->output(), 'status' => 'Worker stopped']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to stop worker'], 500);
        }
    }

    public function restartWorker()
    {
        // Ejecutar el script en segundo plano
        $command = "bash {$this->scriptPath}worker-restart.sh > /dev/null 2>&1 &";
        $result = $this->runDeployCommand($command);

        if ($result->successful()) {
            return response()->json(['output' => $result->output(), 'status' => 'Worker restarted']);
        } else {
            return response()->json(['error' => $result->errorOutput(), 'status' => 'Failed to restart worker'], 500);
        }
    }
}
