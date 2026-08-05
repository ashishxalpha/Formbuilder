<?php

namespace App\Services\Schema;

use Exception;

class SchemaCompiler
{
    protected SchemaValidator $validator;

    public function __construct(SchemaValidator $validator)
    {
        $this->validator = $validator;
    }

    public function compile(array $rawSchema): array
    {
        $schema = $this->normalize($rawSchema);
        
        $validationResult = $this->validator->validate($schema);
        if (!$validationResult['is_valid']) {
            throw new Exception("Invalid schema: " . json_encode($validationResult['errors']));
        }

        $schema = $this->repairDefaults($schema);

        return [
            'compiled_schema' => $schema,
            'validation_rules' => $this->generateValidationRules($schema),
            'renderer_config' => $this->generateRendererConfig($schema),
            'schema_hash' => $this->generateSchemaHash($schema),
            'cache_key' => $this->generateCacheKey($schema),
        ];
    }

    protected function normalize(array $schema): array
    {
        // Ensure core structure exists
        $schema['metadata'] = $schema['metadata'] ?? [];
        $schema['fields'] = $schema['fields'] ?? [];
        return $schema;
    }

    protected function repairDefaults(array $schema): array
    {
        foreach ($schema['fields'] as &$field) {
            $field['required'] = $field['required'] ?? false;
            $field['validation'] = $field['validation'] ?? [];
        }
        return $schema;
    }

    protected function generateValidationRules(array $schema): array
    {
        $rules = [];
        foreach ($schema['fields'] as $field) {
            $fieldRules = [];
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Maps JSON schema types/validations to Laravel rules
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                case 'rating':
                    $fieldRules[] = 'numeric';
                    if (isset($field['validation']['min'])) $fieldRules[] = 'min:' . $field['validation']['min'];
                    if (isset($field['validation']['max'])) $fieldRules[] = 'max:' . $field['validation']['max'];
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    break;
                case 'section_heading':
                    $fieldRules = []; // No validation needed for UI elements
                    break;
                case 'text':
                case 'textarea':
                case 'radio':
                case 'dropdown':
                case 'phone':
                case 'date':
                default:
                    if (!empty($fieldRules)) {
                        $fieldRules[] = 'string';
                    }
                    if (isset($field['validation']['min'])) $fieldRules[] = 'min:' . $field['validation']['min'];
                    if (isset($field['validation']['max'])) $fieldRules[] = 'max:' . $field['validation']['max'];
                    break;
            }
            $rules[$field['key']] = $fieldRules;
        }
        return $rules;
    }

    protected function generateRendererConfig(array $schema): array
    {
        return [
            'theme' => $schema['theme'] ?? ['primary_color' => '#4F46E5'],
            'layout' => $schema['layout'] ?? ['type' => 'linear'],
        ];
    }

    protected function generateSchemaHash(array $schema): string
    {
        return hash('sha256', json_encode($schema));
    }

    protected function generateCacheKey(array $schema): string
    {
        return 'compiled_schema_' . $this->generateSchemaHash($schema);
    }
}
