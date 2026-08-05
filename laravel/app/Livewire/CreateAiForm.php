<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Form;
use App\Models\AiJob;
use App\Jobs\GenerateFormJob;
use Illuminate\Support\Str;

class CreateAiForm extends Component
{
    public $prompt = '';
    public $isOpen = false;
    
    public function openModal()
    {
        $this->isOpen = true;
    }
    
    public function closeModal()
    {
        $this->isOpen = false;
        $this->prompt = '';
    }
    
    public function generate()
    {
        $this->validate([
            'prompt' => 'required|min:10|max:1000'
        ]);
        
        $form = auth()->user()->forms()->create([
            'title' => 'Generating AI Form...',
            'slug' => Str::slug('ai-form-' . time()),
            'status' => 'draft',
        ]);
        
        $aiJob = $form->aiJobs()->create([
            'status' => 'pending',
            'provider' => 'openai',
            'model' => 'gpt-4o',
            'prompt_version' => '1.0',
            'temperature' => 0.7,
        ]);
        
        GenerateFormJob::dispatch($aiJob, $this->prompt);
        
        return redirect()->route('builder', ['form' => $form->id]);
    }

    public function render()
    {
        return view('livewire.create-ai-form');
    }
}
