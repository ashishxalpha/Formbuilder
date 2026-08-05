<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Form;
use App\Services\Builder\BuilderService;
use App\Services\Builder\Commands\AddFieldCommand;
use App\Services\Builder\Commands\DeleteFieldCommand;
use App\Services\Builder\Commands\MoveFieldCommand;
use App\Services\Builder\Commands\UpdateFieldCommand;
use App\Services\Builder\Commands\DuplicateFieldCommand;
use App\Services\Schema\SchemaCompiler;
use Illuminate\Support\Str;

class FormBuilder extends Component
{
    public Form $form;
    public array $schema;
    public string $rawJson;
    public string $activeTab = 'builder'; // builder, json
    public ?string $selectedFieldId = null;
    
    // Autosave tracking
    public string $saveStatus = 'Saved';
    
    protected BuilderService $builderService;
    protected SchemaCompiler $compiler;

    public function boot(BuilderService $builderService, SchemaCompiler $compiler)
    {
        $this->builderService = $builderService;
        $this->compiler = $compiler;
    }

    public function mount(Form $form)
    {
        $this->form = $form;
        $activeVersion = $form->activeVersion;
        $this->schema = $activeVersion ? $activeVersion->schema_data : [
            'version' => '1.0.0',
            'metadata' => ['title' => $form->title],
            'fields' => [],
            'layout' => ['sections' => [['id' => 'section_1', 'fields' => []]]]
        ];
        $this->rawJson = json_encode($this->schema, JSON_PRETTY_PRINT);
    }

    public function updatedRawJson($value)
    {
        // JSON Editor Sync
        try {
            $parsed = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            
            // Validate via compiler
            $compiled = $this->compiler->compile($parsed);
            
            // If it succeeds, update schema
            $this->schema = $parsed;
            $this->saveSchema();
        } catch (\Exception $e) {
            // Invalid JSON or schema, don't update state but maybe show error
            $this->addError('json', $e->getMessage());
        }
    }

    public function addField(string $type)
    {
        $field = [
            'id' => (string) Str::uuid(),
            'key' => 'field_' . time(),
            'type' => $type,
            'label' => 'New ' . ucfirst($type),
            'required' => false,
        ];
        
        $command = new AddFieldCommand($field, 'section_1');
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->syncAndSave();
    }

    public function deleteField(string $fieldId)
    {
        $command = new DeleteFieldCommand($fieldId, 'section_1', $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->selectedFieldId = null;
        $this->syncAndSave();
    }

    public function updateField(string $fieldId, array $newData)
    {
        $command = new UpdateFieldCommand($fieldId, $newData, $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->syncAndSave();
    }
    
    public function duplicateField(string $fieldId)
    {
        $newId = (string) Str::uuid();
        $command = new DuplicateFieldCommand($fieldId, $newId, 'section_1', $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->syncAndSave();
    }

    public function undo()
    {
        $this->schema = $this->builderService->undo($this->schema);
        $this->syncAndSave();
    }

    public function redo()
    {
        $this->schema = $this->builderService->redo($this->schema);
        $this->syncAndSave();
    }

    public function selectField(string $fieldId)
    {
        $this->selectedFieldId = $fieldId;
    }

    public function updateFieldProperty($key, $value)
    {
        if (!$this->selectedFieldId) return;
        
        $currentField = collect($this->schema['fields'])->firstWhere('id', $this->selectedFieldId);
        if ($currentField) {
            $this->updateField($this->selectedFieldId, [$key => $value]);
        }
    }

    protected function syncAndSave()
    {
        $this->rawJson = json_encode($this->schema, JSON_PRETTY_PRINT);
        $this->saveSchema();
    }

    protected function saveSchema()
    {
        $this->saveStatus = 'Saving...';
        
        // Ensure version exists or update it
        $version = $this->form->activeVersion;
        if (!$version) {
            $version = $this->form->versions()->create([
                'schema_data' => $this->schema,
                'schema_hash' => hash('sha256', json_encode($this->schema)),
                'created_by' => auth()->id(),
            ]);
            $this->form->update(['active_version_id' => $version->id]);
        } else {
            $version->update([
                'schema_data' => $this->schema,
                'schema_hash' => hash('sha256', json_encode($this->schema)),
            ]);
        }
        
        $this->saveStatus = 'Saved';
    }

    public function render()
    {
        return view('livewire.form-builder');
    }
}
