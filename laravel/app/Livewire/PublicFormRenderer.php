<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\FormVersion;
use App\Services\Schema\SchemaCompiler;
use Illuminate\Support\Facades\Cache;
use App\Models\Submission;

class PublicFormRenderer extends Component
{
    use WithFileUploads;

    public FormVersion $formVersion;
    public array $compiledData;
    public array $formData = [];
    public bool $isSubmitted = false;

    public function mount(FormVersion $formVersion, SchemaCompiler $compiler)
    {
        $this->formVersion = $formVersion;
        
        $cacheKey = "form_version_{$formVersion->id}_compiled";
        $this->compiledData = Cache::rememberForever($cacheKey, function () use ($compiler, $formVersion) {
            return $compiler->compile($formVersion->schema_data);
        });

        // Initialize state
        foreach ($this->compiledData['compiled_schema']['fields'] as $field) {
            $this->formData[$field['key']] = null;
        }
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->compiledData['validation_rules'] as $fieldKey => $fieldRules) {
            $rules["formData.{$fieldKey}"] = $fieldRules;
        }
        return $rules;
    }

    public function submit()
    {
        $this->validate();

        Submission::create([
            'form_id' => $this->formVersion->form_id,
            'form_version_id' => $this->formVersion->id,
            'response_data' => $this->formData,
        ]);

        // Dispatch domain event (could use Event::dispatch)
        event(new \App\Events\SubmissionReceived($this->formVersion->form_id));

        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.public-form-renderer')->layout('layouts.guest');
    }
}
