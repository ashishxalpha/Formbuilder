<?php

use App\Services\AI\AIClient;
use Illuminate\Support\Facades\Http;
use App\Observability\MetricsLogger;
use Tests\TestCase;

uses(TestCase::class);

test('it handles successful api response', function () {
    Http::fake([
        '*' => Http::response(['schema' => ['version' => '1.0.0']], 200),
    ]);
    
    // Mock metrics logger to avoid actually writing to logs during test if desired
    // Or just let it log.

    $client = new AIClient();
    $result = $client->post('/generate', ['prompt' => 'test']);
    
    expect($result)->toHaveKey('schema');
});

test('it throws exception on server error', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Internal Server Error'], 500),
    ]);

    $client = new AIClient();
    $client->post('/generate', ['prompt' => 'test']);
})->throws(\Exception::class);
