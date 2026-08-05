<?php

namespace App\DTOs;

class GenerateFormRequest
{
    public string $prompt;
    public ?string $model;
    public ?float $temperature;

    public function __construct(string $prompt, ?string $model = null, ?float $temperature = null)
    {
        $this->prompt = $prompt;
        $this->model = $model;
        $this->temperature = $temperature;
    }
}
