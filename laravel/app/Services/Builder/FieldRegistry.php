<?php

namespace App\Services\Builder;

use App\Services\Builder\Fields\FieldInterface;

class FieldRegistry
{
    protected array $fields = [];

    public function register(string $type, string $fieldClass): void
    {
        if (!is_subclass_of($fieldClass, FieldInterface::class)) {
            throw new \InvalidArgumentException("Class {$fieldClass} must implement FieldInterface.");
        }
        $this->fields[$type] = $fieldClass;
    }

    public function getFieldClass(string $type): ?string
    {
        return $this->fields[$type] ?? null;
    }

    public function getAllRegisteredTypes(): array
    {
        return array_keys($this->fields);
    }
}
