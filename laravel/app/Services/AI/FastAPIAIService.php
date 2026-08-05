<?php

namespace App\Services\AI;

use App\Contracts\AIServiceInterface;
use App\DTOs\GenerateFormRequest;
use App\DTOs\GenerateFormResponse;
use App\DTOs\EditFormResponse;
use App\DTOs\RepairSchemaResponse;

class FastAPIAIService implements AIServiceInterface
{
    protected AIClient $client;

    public function __construct(AIClient $client)
    {
        $this->client = $client;
    }

    public function generateForm(GenerateFormRequest $request): GenerateFormResponse
    {
        $data = $this->client->post('/forms/generate', [
            'prompt' => $request->prompt,
            'model' => $request->model,
            'temperature' => $request->temperature,
        ]);

        return new GenerateFormResponse(
            $data['schema'] ?? [],
            $data['input_tokens'] ?? 0,
            $data['output_tokens'] ?? 0,
            $data['prompt_version'] ?? 'unknown'
        );
    }

    public function editForm(string $prompt, array $currentSchema): EditFormResponse
    {
        $data = $this->client->post('/forms/edit', [
            'prompt' => $prompt,
            'schema' => $currentSchema,
        ]);

        return new EditFormResponse(
            $data['schema'] ?? [],
            $data['input_tokens'] ?? 0,
            $data['output_tokens'] ?? 0
        );
    }

    public function repairSchema(string $malformedJson, string $errorDetails): RepairSchemaResponse
    {
        $data = $this->client->post('/forms/repair', [
            'malformed_json' => $malformedJson,
            'error_details' => $errorDetails,
        ]);

        return new RepairSchemaResponse(
            $data['schema'] ?? [],
            $data['success'] ?? false,
            $data['error_message'] ?? null
        );
    }
}
