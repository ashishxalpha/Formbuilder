<?php

namespace App\DTOs;

class EditFormResponse
{
    public array $schema;
    public int $inputTokens;
    public int $outputTokens;

    public function __construct(array $schema, int $inputTokens, int $outputTokens)
    {
        $this->schema = $schema;
        $this->inputTokens = $inputTokens;
        $this->outputTokens = $outputTokens;
    }
}
