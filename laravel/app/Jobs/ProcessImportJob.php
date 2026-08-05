<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ImportJob;
use App\Services\AI\AIClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes

    public function __construct(
        public ImportJob $importJob
    ) {}

    public function handle(AIClient $aiClient): void
    {
        try {
            $this->importJob->update(['status' => 'processing']);

            $fullPath = Storage::disk('local')->path($this->importJob->file_path);
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found at path: {$this->importJob->file_path}");
            }

            $endpoint = $this->importJob->type === 'docx' ? '/imports/docx' : '/imports/xlsx';
            
            $filename = basename($fullPath);
            $response = $aiClient->upload($endpoint, $fullPath, $filename);

            $this->importJob->update([
                'status' => 'preview',
                'schema' => [
                    'schema' => $response['schema'] ?? null,
                    'warnings' => $response['warnings'] ?? []
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Import processing failed", ['import_id' => $this->importJob->id, 'error' => $e->getMessage()]);
            $this->importJob->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
