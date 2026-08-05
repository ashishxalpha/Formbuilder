<?php

namespace App\Services\Schema;

class SchemaValidator
{
    public function validate(array $schema): array
    {
        $errors = [];

        if (!isset($schema['fields']) || !is_array($schema['fields'])) {
            return ['is_valid' => false, 'errors' => ['Missing or invalid fields array']];
        }

        $keys = [];
        foreach ($schema['fields'] as $index => $field) {
            if (!isset($field['key'])) {
                $errors[] = "Field at index {$index} is missing a key.";
                continue;
            }

            if (in_array($field['key'], $keys)) {
                $errors[] = "Duplicate key detected: {$field['key']}";
            }
            $keys[] = $field['key'];

            if (!isset($field['type'])) {
                $errors[] = "Field '{$field['key']}' is missing a type.";
            }

            // Check options for specific fields
            if (in_array($field['type'] ?? '', ['dropdown', 'radio', 'checkbox'])) {
                if (!isset($field['options']) || !is_array($field['options'])) {
                    $errors[] = "Field '{$field['key']}' requires an options array.";
                }
            }
        }

        return [
            'is_valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }
}
