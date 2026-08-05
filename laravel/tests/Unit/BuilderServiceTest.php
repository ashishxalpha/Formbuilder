<?php

use App\Services\Builder\BuilderService;
use App\Services\Builder\Commands\AddFieldCommand;
use App\Services\Builder\Commands\DeleteFieldCommand;
use App\Services\Builder\Commands\UpdateFieldCommand;
use App\Services\Builder\Commands\MoveFieldCommand;

beforeEach(function () {
    $this->service = new BuilderService();
    $this->initialSchema = [
        'fields' => [],
        'layout' => ['sections' => [['id' => 's1', 'fields' => []]]]
    ];
});

test('it can add a field', function () {
    $field = ['id' => 'f1', 'type' => 'text'];
    $command = new AddFieldCommand($field, 's1');
    
    $schema = $this->service->executeCommand($command, $this->initialSchema);
    
    expect($schema['fields'])->toHaveCount(1);
    expect($schema['fields'][0]['id'])->toBe('f1');
    expect($schema['layout']['sections'][0]['fields'])->toContain('f1');
});

test('it can undo an add field command', function () {
    $field = ['id' => 'f1', 'type' => 'text'];
    $command = new AddFieldCommand($field, 's1');
    
    $schema = $this->service->executeCommand($command, $this->initialSchema);
    expect($schema['fields'])->toHaveCount(1);
    
    $schema = $this->service->undo($schema);
    expect($schema['fields'])->toHaveCount(0);
});

test('it can redo an add field command', function () {
    $field = ['id' => 'f1', 'type' => 'text'];
    $command = new AddFieldCommand($field, 's1');
    
    $schema = $this->service->executeCommand($command, $this->initialSchema);
    $schema = $this->service->undo($schema);
    $schema = $this->service->redo($schema);
    
    expect($schema['fields'])->toHaveCount(1);
});

test('it can update a field', function () {
    $field = ['id' => 'f1', 'type' => 'text', 'label' => 'Old'];
    $command = new AddFieldCommand($field, 's1');
    $schema = $this->service->executeCommand($command, $this->initialSchema);
    
    $updateCmd = new UpdateFieldCommand('f1', ['label' => 'New'], $schema);
    $schema = $this->service->executeCommand($updateCmd, $schema);
    
    expect($schema['fields'][0]['label'])->toBe('New');
    
    $schema = $this->service->undo($schema);
    expect($schema['fields'][0]['label'])->toBe('Old');
});
