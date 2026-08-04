<?php

namespace App\Observability;

use Illuminate\Support\Facades\Log;

class MetricsLogger
{
    public static function record(string $metricName, int|float $value, array $tags = []): void
    {
        Log::channel('metrics')->info($metricName, [
            'value' => $value,
            'tags' => $tags,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public static function increment(string $metricName, array $tags = []): void
    {
        self::record($metricName, 1, $tags);
    }
}
