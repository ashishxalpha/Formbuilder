<?php

namespace App\DTOs;

class GenerateFormResponse
{
    public array $schema;
    public int $inputTokens;
    public int $outputTokens;
    public string $promptVersion;

    public function __construct(array $schema, int $inputTokens, int $outputTokens, string $promptVersion)
    {
        $this->schema = $schema;
        $this->inputTokens = $inputTokens;
        $this->outputTokens = $outputTokens;
        $this->promptVersion = $promptVersion;
    }
}
