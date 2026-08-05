<?php

namespace App\DTOs;

class ImportResponse
{
    public array $schema;
    public array $warnings;

    public function __construct(array $schema, array $warnings = [])
    {
        $this->schema = $schema;
        $this->warnings = $warnings;
    }
}
