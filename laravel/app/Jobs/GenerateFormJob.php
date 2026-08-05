<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AiJob;
use App\Models\Form;
use App\Services\AI\AIClient;
use App\Services\Schema\SchemaCompiler;
use Illuminate\Support\Facades\Log;

class GenerateFormJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    
    protected AiJob $aiJob;
    protected string $prompt;

    public function __construct(AiJob $aiJob, string $prompt)
    {
        $this->aiJob = $aiJob;
        $this->prompt = $prompt;
    }

    public function handle(AIClient $client, SchemaCompiler $compiler)
    {
        $this->aiJob->update(['status' => 'processing', 'started_at' => now()]);
        
        try {
            // 1. Call FastAPI to generate schema
            $response = $client->post('/generate', [
                'prompt' => $this->prompt,
                'model' => $this->aiJob->model,
                'temperature' => (float)$this->aiJob->temperature
            ]);

            $rawSchema = $response['schema'];
            
            // 2. Strict Validation via SchemaCompiler
            try {
                $compiled = $compiler->compile($rawSchema);
                $finalSchema = $compiled['compiled_schema'];
            } catch (\Exception $e) {
                Log::warning("AI Schema Validation Failed. Attempting Repair.", ['error' => $e->getMessage()]);
                
                // 3. Fallback: Repair malformed schema via FastAPI
                $repairResponse = $client->post('/repair', [
                    'malformed_json' => is_string($rawSchema) ? $rawSchema : json_encode($rawSchema),
                    'error_details' => $e->getMessage()
                ]);
                
                if (!$repairResponse['success']) {
                    throw new \Exception("Failed to repair schema: " . $repairResponse['error_message']);
                }
                
                $finalSchema = $compiler->compile($repairResponse['schema'])['compiled_schema'];
            }

            // 4. Save successful schema
            $form = $this->aiJob->form;
            $version = $form->versions()->create([
                'schema_data' => $finalSchema,
                'schema_hash' => hash('sha256', json_encode($finalSchema)),
                'created_by' => $form->user_id,
                'change_summary' => 'AI Generated'
            ]);
            
            $form->update([
                'active_version_id' => $version->id,
                'title' => $finalSchema['metadata']['title'] ?? 'AI Form',
                'description' => $finalSchema['metadata']['description'] ?? ''
            ]);

            $this->aiJob->update([
                'status' => 'completed',
                'completed_at' => now(),
                'input_tokens' => $response['input_tokens'] ?? 0,
                'output_tokens' => $response['output_tokens'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error("GenerateFormJob failed: " . $e->getMessage());
            $this->aiJob->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
