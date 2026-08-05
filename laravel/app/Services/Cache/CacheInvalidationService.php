<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class CacheInvalidationService
{
    public static function invalidateCompiledSchema(int $formId): void
    {
        Cache::tags(['schemas'])->forget("form_{$formId}_schema");
    }

    public static function invalidatePublicForm(string $slug): void
    {
        Cache::tags(['public_forms'])->forget("form_slug_{$slug}");
    }

    public static function invalidateTemplateList(): void
    {
        Cache::tags(['templates'])->flush();
    }
}
