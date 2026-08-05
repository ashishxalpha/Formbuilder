<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'type',
        'status',
        'file_path',
        'error',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
