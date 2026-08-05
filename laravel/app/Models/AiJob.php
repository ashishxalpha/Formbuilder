<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'status',
        'provider',
        'model',
        'prompt_version',
        'temperature',
        'input_tokens',
        'output_tokens',
        'cost',
        'latency_ms',
        'retries',
        'error',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'cost' => 'decimal:4',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
