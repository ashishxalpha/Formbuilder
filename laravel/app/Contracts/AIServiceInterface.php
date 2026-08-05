<?php

namespace App\Contracts;

use App\DTOs\GenerateFormRequest;
use App\DTOs\GenerateFormResponse;
use App\DTOs\EditFormResponse;
use App\DTOs\RepairSchemaResponse;

interface AIServiceInterface
{
    /**
     * Generate a new form schema based on a prompt.
     */
    public function generateForm(GenerateFormRequest $request): GenerateFormResponse;

    /**
     * Edit an existing form schema based on a prompt.
     */
    public function editForm(string $prompt, array $currentSchema): EditFormResponse;

    /**
     * Attempt to repair a malformed JSON schema.
     */
    public function repairSchema(string $malformedJson, string $errorDetails): RepairSchemaResponse;
}
