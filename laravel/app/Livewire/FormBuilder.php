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
    public ?string $draggingFieldId = null;
    public bool $isPublished = false;
    
    // Sidebar editing state
    public string $editingFieldLabel = '';
    public string $editingFieldKey = '';
    public bool $editingFieldRequired = false;
    public array $editingFieldOptions = [];
    public string $editingFieldPlaceholder = '';
    public string $editingFieldHelpText = '';
    public string $editingFieldDefaultValue = '';
    public ?int $editingFieldValidationMin = null;
    public ?int $editingFieldValidationMax = null;
    
    public int $currentStep = 1; // 1: Details, 2: Builder, 3: Settings, 4: Finish
    
    // Livewire History State for Undo/Redo
    public array $historyStack = [];
    public int $historyIndex = -1;
    
    // Autosave tracking
    public string $saveStatus = 'Saved';
    
    // AI Generation tracking
    public bool $hasPendingAiJob = false;
    public ?string $aiJobStatus = null;
    
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
            $this->syncAndSave();
        }
    }
    
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->syncAndSave();
        }
    }
    
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
        
        $this->checkAiJobStatus();
        
        $version = $form->activeVersion;
        if ($version) {
            $this->schema = $version->schema_data;
        } else {
            $this->schema = [
                'version' => '1.0.0',
                'metadata' => [
                    'title' => 'Untitled Form',
                    'description' => ''
                ],
                'fields' => [],
                'layout' => [
                    'sections' => [
                        ['id' => 'section_1', 'fields' => []]
                    ]
                ]
            ];
        }
        
        $this->rawJson = json_encode($this->schema, JSON_PRETTY_PRINT);
        $this->isPublished = $form->status === 'published';
        
        $this->pushHistory();
    }
    
    public function checkAiJobStatus()
    {
        $job = $this->form->aiJobs()->latest()->first();
        if ($job && in_array($job->status, ['pending', 'processing'])) {
            $this->hasPendingAiJob = true;
            $this->aiJobStatus = $job->status === 'pending' ? 'Waiting in queue...' : 'AI is thinking...';
        } else {
            if ($this->hasPendingAiJob) {
                // Job just finished, reload schema
                $this->form->refresh();
                $version = $this->form->activeVersion;
                if ($version) {
                    $this->schema = $version->schema_data;
                    $this->rawJson = json_encode($this->schema, JSON_PRETTY_PRINT);
                    $this->pushHistory();
                }
            }
            $this->hasPendingAiJob = false;
            $this->aiJobStatus = null;
        }
    }

    public function pushHistory()
    {
        if ($this->historyIndex < count($this->historyStack) - 1) {
            $this->historyStack = array_slice($this->historyStack, 0, $this->historyIndex + 1);
        }
        $this->historyStack[] = $this->schema;
        $this->historyIndex++;
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
            'label' => 'New ' . ucfirst(str_replace('_', ' ', $type)),
            'required' => false,
        ];
        
        if (in_array($type, ['dropdown', 'radio', 'checkbox'])) {
            $field['options'] = [
                ['label' => 'Option 1', 'value' => 'option_1'],
                ['label' => 'Option 2', 'value' => 'option_2'],
            ];
        }
        
        $command = new AddFieldCommand($field, 'section_1');
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->pushHistory();
        $this->syncAndSave();
        $this->selectField($field['id']);
    }

    public function deleteField(string $fieldId)
    {
        $command = new DeleteFieldCommand($fieldId, 'section_1', $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        if ($this->selectedFieldId === $fieldId) $this->selectedFieldId = null;
        $this->pushHistory();
        $this->syncAndSave();
    }
    
    public function dragStart($fieldId)
    {
        $this->draggingFieldId = $fieldId;
    }
    
    public function dragEnter($fieldId)
    {
        // visual feedback could go here
    }
    
    public function drop($targetFieldId)
    {
        if (!$this->draggingFieldId || $this->draggingFieldId === $targetFieldId) {
            $this->draggingFieldId = null;
            return;
        }
        
        $fields = collect($this->schema['fields']);
        $sourceIndex = $fields->search(fn($f) => $f['id'] === $this->draggingFieldId);
        $targetIndex = $fields->search(fn($f) => $f['id'] === $targetFieldId);
        
        if ($sourceIndex !== false && $targetIndex !== false) {
            $sourceField = $fields->pull($sourceIndex);
            $fields->splice($targetIndex, 0, [$sourceField]);
            
            $this->schema['fields'] = $fields->values()->all();
            
            // Sync layout sections
            if (isset($this->schema['layout']['sections'][0]['fields'])) {
                $this->schema['layout']['sections'][0]['fields'] = collect($this->schema['fields'])->pluck('id')->all();
            }
            
            $this->pushHistory();
            $this->syncAndSave();
        }
        
        $this->draggingFieldId = null;
    }

    public function updateField(string $fieldId, array $newData)
    {
        $command = new UpdateFieldCommand($fieldId, $newData, $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->pushHistory();
        $this->syncAndSave();
    }
    
    public function duplicateField(string $fieldId)
    {
        $newId = (string) Str::uuid();
        $command = new DuplicateFieldCommand($fieldId, $newId, 'section_1', $this->schema);
        $this->schema = $this->builderService->executeCommand($command, $this->schema);
        $this->pushHistory();
        $this->syncAndSave();
    }

    public function undo()
    {
        if ($this->historyIndex > 0) {
            $this->historyIndex--;
            $this->schema = $this->historyStack[$this->historyIndex];
            
            // Re-sync selected field if it still exists
            if ($this->selectedFieldId) {
                $this->selectField($this->selectedFieldId);
            }
            
            $this->syncAndSave(false);
        }
    }

    public function redo()
    {
        if ($this->historyIndex < count($this->historyStack) - 1) {
            $this->historyIndex++;
            $this->schema = $this->historyStack[$this->historyIndex];
            
            // Re-sync selected field if it exists
            if ($this->selectedFieldId) {
                $this->selectField($this->selectedFieldId);
            }
            
            $this->syncAndSave(false);
        }
    }

    public function selectField(string $fieldId)
    {
        $this->selectedFieldId = $fieldId;
        $currentField = collect($this->schema['fields'])->firstWhere('id', $fieldId);
        if ($currentField) {
            $this->editingFieldLabel = $currentField['label'] ?? '';
            $this->editingFieldKey = $currentField['key'] ?? '';
            $this->editingFieldRequired = $currentField['required'] ?? false;
            $this->editingFieldOptions = $currentField['options'] ?? [];
            $this->editingFieldPlaceholder = $currentField['placeholder'] ?? '';
            $this->editingFieldHelpText = $currentField['help_text'] ?? '';
            $this->editingFieldDefaultValue = $currentField['default'] ?? '';
            $this->editingFieldValidationMin = $currentField['validation']['min'] ?? null;
            $this->editingFieldValidationMax = $currentField['validation']['max'] ?? null;
        } else {
            $this->selectedFieldId = null;
        }
    }
    
    public function updatedEditingFieldLabel($value)
    {
        $this->updateFieldProperty('label', $value);
    }
    
    public function updatedEditingFieldKey($value)
    {
        $this->updateFieldProperty('key', $value);
    }
    
    public function updatedEditingFieldRequired($value)
    {
        $this->updateFieldProperty('required', $value);
    }
    
    public function updatedEditingFieldPlaceholder($value)
    {
        $this->updateFieldProperty('placeholder', $value);
    }
    
    public function updatedEditingFieldHelpText($value)
    {
        $this->updateFieldProperty('help_text', $value);
    }
    
    public function updatedEditingFieldDefaultValue($value)
    {
        $this->updateFieldProperty('default', $value);
    }
    
    protected function getValidationObject()
    {
        if (!$this->selectedFieldId) return [];
        $currentField = collect($this->schema['fields'])->firstWhere('id', $this->selectedFieldId);
        return $currentField['validation'] ?? [];
    }
    
    public function updatedEditingFieldValidationMin($value)
    {
        $val = $this->getValidationObject();
        $val['min'] = $value === '' ? null : (int)$value;
        $this->updateFieldProperty('validation', $val);
    }
    
    public function updatedEditingFieldValidationMax($value)
    {
        $val = $this->getValidationObject();
        $val['max'] = $value === '' ? null : (int)$value;
        $this->updateFieldProperty('validation', $val);
    }
    
    public function updatedIsPublished($value)
    {
        $this->form->update([
            'status' => $value ? 'published' : 'draft'
        ]);
        $this->saveStatus = 'Saved';
    }
    
    public function addOption()
    {
        $this->editingFieldOptions[] = [
            'label' => 'New Option',
            'value' => 'option_' . time()
        ];
        $this->updateFieldProperty('options', $this->editingFieldOptions);
    }
    
    public function removeOption($index)
    {
        if (isset($this->editingFieldOptions[$index])) {
            unset($this->editingFieldOptions[$index]);
            $this->editingFieldOptions = array_values($this->editingFieldOptions); // Re-index
            $this->updateFieldProperty('options', $this->editingFieldOptions);
        }
    }
    
    public function updatedEditingFieldOptions($value, $key)
    {
        // When an option label or value is edited
        $this->updateFieldProperty('options', $this->editingFieldOptions);
    }

    public function updateFieldProperty($key, $value)
    {
        if (!$this->selectedFieldId) return;
        
        $currentField = collect($this->schema['fields'])->firstWhere('id', $this->selectedFieldId);
        if ($currentField) {
            $this->updateField($this->selectedFieldId, [$key => $value]);
        }
    }

    protected function syncAndSave($runSave = true)
    {
        $this->rawJson = json_encode($this->schema, JSON_PRETTY_PRINT);
        if ($runSave) {
            $this->saveSchema();
        }
    }

    public function saveSchema()
    {
        $this->saveStatus = 'Saving...';
        
        // Sync the title to the parent form model for the dashboard
        if (isset($this->schema['metadata']['title'])) {
            $this->form->update([
                'title' => $this->schema['metadata']['title'],
                'description' => $this->schema['metadata']['description'] ?? $this->form->description,
            ]);
        }
        
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
