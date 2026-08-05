<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Form;
use App\Models\ActivityLog;
use App\Models\AiJob;
use Livewire\WithPagination;

class FormDashboard extends Component
{
    use WithPagination;

    public Form $form;
    public string $search = '';

    public function mount(Form $form)
    {
        $this->form = $form;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function exportCsv()
    {
        $submissions = $this->form->submissions()->latest()->get();
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$this->form->slug}-submissions.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            
            $columns = ['ID', 'Submitted At'];
            $dataKeys = [];
            
            $schemaFields = $this->form->activeVersion->schema_data['fields'] ?? [];
            $fieldMap = collect($schemaFields)->pluck('label', 'key')->toArray();
            
            // Build headers from the actual schema to ensure all fields are captured
            foreach ($schemaFields as $field) {
                if (in_array($field['type'], ['section_heading'])) continue;
                $columns[] = $field['label'] ?? $field['key'];
                $dataKeys[] = $field['key'];
            }
            
            fputcsv($file, $columns);
            
            foreach ($submissions as $submission) {
                $row = [
                    $submission->id,
                    $submission->created_at->format('Y-m-d H:i:s')
                ];
                $data = $submission->response_data ?? [];
                
                foreach ($dataKeys as $key) {
                    $val = $data[$key] ?? '';
                    if (is_array($val)) {
                        $resolved = array_map(fn($v) => $this->resolveOptionLabel($key, $v, $schemaFields), $val);
                        $val = implode(', ', $resolved);
                    } else {
                        $val = $this->resolveOptionLabel($key, $val, $schemaFields);
                    }
                    $row[] = $val;
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    public function resolveOptionLabel($fieldKey, $value, $schemaFields)
    {
        if ($value === '' || $value === null) return '';
        
        $field = collect($schemaFields)->firstWhere('key', $fieldKey);
        if (!$field || !isset($field['options'])) return $value;
        
        $option = collect($field['options'])->firstWhere('value', $value);
        return $option ? ($option['label'] ?? $value) : $value;
    }

    public function formatSubmissionData($responseData)
    {
        if (!is_array($responseData)) return '';
        
        $schemaFields = $this->form->activeVersion->schema_data['fields'] ?? [];
        $fieldMap = collect($schemaFields)->pluck('label', 'key')->toArray();
        
        $formatted = [];
        foreach ($responseData as $key => $value) {
            $label = $fieldMap[$key] ?? $key;
            if (is_array($value)) {
                $resolved = array_map(fn($v) => $this->resolveOptionLabel($key, $v, $schemaFields), $value);
                $value = implode(', ', $resolved);
            } else {
                $value = $this->resolveOptionLabel($key, $value, $schemaFields);
            }
            
            // Skip empty file uploads or empty strings to keep it clean
            if ($value === '' || $value === null) continue;
            
            $formatted[] = "<span class='font-semibold text-gray-700 dark:text-gray-300'>{$label}:</span> <span class='text-gray-600 dark:text-gray-400'>{$value}</span>";
        }
        
        return implode(' <span class="text-gray-300 dark:text-gray-600 mx-1">|</span> ', $formatted);
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
            
        $query = $this->form->submissions()->latest();
        if (!empty($this->search)) {
            $query->where('response_data', 'LIKE', '%' . $this->search . '%');
        }
        $submissions = $query->paginate(10);

        return view('livewire.form-dashboard', [
            'activities' => $activities,
            'aiJobs' => $aiJobs,
            'submissions' => $submissions,
        ]);
    }
}
