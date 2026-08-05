<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Form;
use App\Models\ActivityLog;
use App\Models\AiJob;

class FormDashboard extends Component
{
    public Form $form;

    public function mount(Form $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $activities = ActivityLog::where('form_id', $this->form->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $aiJobs = AiJob::where('form_id', $this->form->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.form-dashboard', [
            'activities' => $activities,
            'aiJobs' => $aiJobs,
        ]);
    }
}
