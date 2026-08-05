<?php

use App\Services\Schema\SchemaCompiler;
use App\Services\Schema\SchemaValidator;

test('it normalizes and compiles a valid schema', function () {
    $validator = Mockery::mock(SchemaValidator::class);
    $validator->shouldReceive('validate')->andReturn(['is_valid' => true, 'errors' => []]);

    $compiler = new SchemaCompiler($validator);

    $rawSchema = [
        'fields' => [
            ['key' => 'email', 'type' => 'email', 'required' => true]
        ]
    ];

    $compiled = $compiler->compile($rawSchema);

    expect($compiled)->toHaveKeys(['compiled_schema', 'validation_rules', 'renderer_config', 'schema_hash', 'cache_key']);
    expect($compiled['validation_rules']['email'])->toContain('required', 'email');
});

test('it throws exception on invalid schema', function () {
    $validator = Mockery::mock(SchemaValidator::class);
    $validator->shouldReceive('validate')->andReturn(['is_valid' => false, 'errors' => ['Missing key']]);

    $compiler = new SchemaCompiler($validator);

    $compiler->compile([]);
})->throws(Exception::class);
