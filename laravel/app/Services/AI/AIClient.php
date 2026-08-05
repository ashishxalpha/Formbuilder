<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use App\Observability\PerformanceTimer;
use App\Observability\MetricsLogger;

class AIClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected int $retries;

    public function __construct()
    {
        $this->baseUrl = config('services.ai.url', 'http://fastapi:8000');
        $this->apiKey = config('services.ai.key', 'secret');
        $this->timeout = config('services.ai.timeout', 30);
        $this->retries = config('services.ai.retries', 3);
    }

    public function post(string $endpoint, array $payload): array
    {
        return PerformanceTimer::measure("ai_client_post_$endpoint", function () use ($endpoint, $payload) {
            try {
                Log::info("AI Client Request: $endpoint", ['payload' => $payload]);

                $response = Http::withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Accept' => 'application/json',
                    ])
                    ->timeout($this->timeout)
                    ->retry($this->retries, 500)
                    ->post($this->baseUrl . $endpoint, $payload);

                $response->throw();

                $data = $response->json();
                Log::info("AI Client Response: $endpoint", ['response' => $data]);
                
                MetricsLogger::increment('ai_request_success');

                return $data;

            } catch (RequestException $e) {
                MetricsLogger::increment('ai_request_error');
                Log::error("AI Client Error: $endpoint", [
                    'error' => $e->getMessage(),
                    'response' => $e->response?->json(),
                ]);
                throw new \Exception("AI Service Error: " . $e->getMessage());
            } catch (\Exception $e) {
                MetricsLogger::increment('ai_request_failure');
                Log::error("AI Client Fatal Error: $endpoint", [
                    'error' => $e->getMessage(),
                ]);
                throw new \Exception("AI Gateway Unreachable: " . $e->getMessage());
            }
        });
    }
}
