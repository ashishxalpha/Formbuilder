<?php

namespace App\Observability;

use Closure;

class PerformanceTimer
{
    public static function measure(string $metricName, Closure $callback, array $tags = [])
    {
        $startTime = microtime(true);
        
        $result = $callback();
        
        $duration = microtime(true) - $startTime;
        $durationMs = round($duration * 1000, 2);
        
        MetricsLogger::record($metricName . '_latency_ms', $durationMs, $tags);
        
        return $result;
    }
}
