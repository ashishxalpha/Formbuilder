<?php

namespace App\Services;

use App\Models\Template;
use App\Models\FormVersion;

class TemplateService
{
    public function saveAsTemplate(FormVersion $version, string $name, ?string $category, bool $isPublic, int $userId): Template
    {
        return Template::create([
            'name' => $name,
            'category' => $category,
            'schema' => $version->schema_data,
            'is_public' => $isPublic,
            'created_by' => $userId,
        ]);
    }

    public function search(string $query = '', ?string $category = null, bool $onlyPublic = true)
    {
        $q = Template::query();
        
        if ($onlyPublic) {
            $q->where('is_public', true);
        }

        if ($query) {
            $q->where('name', 'like', "%{$query}%");
        }

        if ($category) {
            $q->where('category', $category);
        }

        return $q->get();
    }
}
