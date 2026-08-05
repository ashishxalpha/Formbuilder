<?php

use App\DTOs\GenerateFormRequest;
use App\DTOs\GenerateFormResponse;

test('GenerateFormRequest constructs correctly', function () {
    $dto = new GenerateFormRequest('Create a contact form', 'gpt-4o', 0.5);
    
    expect($dto->prompt)->toBe('Create a contact form');
    expect($dto->model)->toBe('gpt-4o');
    expect($dto->temperature)->toBe(0.5);
});

test('GenerateFormResponse constructs correctly', function () {
    $dto = new GenerateFormResponse(['version' => '1.0'], 10, 20, 'v1');
    
    expect($dto->schema)->toBe(['version' => '1.0']);
    expect($dto->inputTokens)->toBe(10);
    expect($dto->outputTokens)->toBe(20);
    expect($dto->promptVersion)->toBe('v1');
});
