<?php

namespace App\DTOs;

class RepairSchemaResponse
{
    public array $schema;
    public bool $success;
    public ?string $errorMessage;

    public function __construct(array $schema, bool $success, ?string $errorMessage = null)
    {
        $this->schema = $schema;
        $this->success = $success;
        $this->errorMessage = $errorMessage;
    }
}
