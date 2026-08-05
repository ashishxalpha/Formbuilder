<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'schema_data',
        'schema_hash',
        'created_by',
        'change_summary',
        'is_published',
    ];

    protected $casts = [
        'schema_data' => 'array',
        'is_published' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
