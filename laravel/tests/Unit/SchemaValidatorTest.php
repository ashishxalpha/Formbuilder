<?php

use App\Services\Schema\SchemaValidator;

test('it passes a valid schema', function () {
    $validator = new SchemaValidator();
    
    $schema = [
        'fields' => [
            ['key' => 'name', 'type' => 'text']
        ]
    ];
    
    $result = $validator->validate($schema);
    
    expect($result['is_valid'])->toBeTrue();
    expect($result['errors'])->toBeEmpty();
});

test('it detects duplicate keys', function () {
    $validator = new SchemaValidator();
    
    $schema = [
        'fields' => [
            ['key' => 'name', 'type' => 'text'],
            ['key' => 'name', 'type' => 'email'],
        ]
    ];
    
    $result = $validator->validate($schema);
    
    expect($result['is_valid'])->toBeFalse();
    expect($result['errors'][0])->toContain('Duplicate key detected');
});
