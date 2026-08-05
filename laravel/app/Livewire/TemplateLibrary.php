<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TemplateService;

class TemplateLibrary extends Component
{
    public string $searchQuery = '';
    public string $category = '';
    
    protected TemplateService $templateService;

    public function boot(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function duplicateTemplate($templateId)
    {
        $template = \App\Models\Template::findOrFail($templateId);
        
        $form = \App\Models\Form::create([
            'user_id' => auth()->id(),
            'title' => $template->name . ' (Copy)',
            'slug' => \Illuminate\Support\Str::slug($template->name . ' ' . time()),
            'status' => 'draft',
        ]);
        
        $version = $form->versions()->create([
            'schema_data' => $template->schema,
            'schema_hash' => hash('sha256', json_encode($template->schema)),
            'created_by' => auth()->id(),
            'change_summary' => 'Created from template',
        ]);
        
        $form->update(['active_version_id' => $version->id]);
        
        return redirect()->route('builder', ['form' => $form->id]);
    }

    public function render()
    {
        $templates = $this->templateService->search($this->searchQuery, $this->category, true);
        return view('livewire.template-library', ['templates' => $templates]);
    }
}
