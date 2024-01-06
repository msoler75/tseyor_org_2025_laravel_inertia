<?php

namespace App\Pigmalion;

class Profiler
{
    private static $calls = [];

    public static function calling($identifier)
    {
        if (!isset(self::$calls[$identifier])) {
            self::$calls[$identifier] = [
                'count' => 0,
                'totalTime' => 0,
                'minTime' => null,
                'maxTime' => null,
            ];
        }

        $startTime = microtime(true);

        return function () use ($identifier, $startTime) {
            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            self::$calls[$identifier]['count']++;
            self::$calls[$identifier]['totalTime'] += $duration;

            if (self::$calls[$identifier]['minTime'] === null || $duration < self::$calls[$identifier]['minTime']) {
                self::$calls[$identifier]['minTime'] = $duration;
            }

            if (self::$calls[$identifier]['maxTime'] === null || $duration > self::$calls[$identifier]['maxTime']) {
                self::$calls[$identifier]['maxTime'] = $duration;
            }
        };
    }

    public static function results()
    {
        $results = [];

        foreach (self::$calls as $identifier => $data) {
            $count = $data['count'];
            $totalTime = $data['totalTime'];
            $minTime = $data['minTime'];
            $maxTime = $data['maxTime'];

            $results[] = [
                'Identifier' => $identifier,
                'Count' => $count,
                'Total Time' => $totalTime,
                'Min Time' => $minTime,
                'Max Time' => $maxTime,
            ];
        }

        return $results;
    }
}
