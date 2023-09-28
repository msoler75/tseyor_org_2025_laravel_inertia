<?php

namespace App;


// es un profiler

class T
{
    protected $startTime;
    protected $str;

    public static $stats = [];

    public function __construct($classname, $str)
    {
        $this->startTime = microtime(true);
        $this->str = $classname."::".$str;
    }

    public function __destruct()
    {
        $endTime = microtime(true);
        $executionTime = ($endTime - $this->startTime) * 1000;

        self::$stats[] = [
            'str' => $this->str,
            'time' => $executionTime,
        ];
    }

    public static function xprint() {

        $methodStats = [];

        echo "<pre>";

        foreach (self::$stats as $stat) {
            $methodName = $stat['str'];
            $executionTime = $stat['time'];

            if (!isset($methodStats[$methodName])) {
                $methodStats[$methodName] = [
                    'str' => $methodName,
                    'count' => 1,
                    'totalTime' => $executionTime,
                ];
            } else {
                $methodStats[$methodName]['count']++;
                $methodStats[$methodName]['totalTime'] += $executionTime;
            }
        }

         // Ordenar el arreglo $methodStats de mayor a menor tiempo de ejecución
    usort($methodStats, function ($a, $b) {
        return 1000*$b['totalTime'] - 1000*$a['totalTime'];
    });

        foreach ($methodStats as $methodName => $stats) {
            echo "Method: ".$stats['str'] . PHP_EOL;
            echo "Call count: " . $stats['count'] . PHP_EOL;
            echo "Total time: " . $stats['totalTime'] . " milliseconds" . PHP_EOL;
            echo PHP_EOL;
        }

        echo "</pre>";

        die;

    }
}
