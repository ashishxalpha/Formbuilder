<?php

namespace App\Services\Builder\Fields;

class TextField implements FieldInterface
{
    public static function getType(): string
    {
        return 'text';
    }

    public function getValidationRules(array $fieldSchema): array
    {
        $rules = ['string'];
        if ($fieldSchema['required'] ?? false) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        if (isset($fieldSchema['validation']['min'])) {
            $rules[] = 'min:' . $fieldSchema['validation']['min'];
        }
        if (isset($fieldSchema['validation']['max'])) {
            $rules[] = 'max:' . $fieldSchema['validation']['max'];
        }

        return $rules;
    }

    public function getMetadata(): array
    {
        return [
            'icon' => 'text-icon',
            'label' => 'Short Text',
            'description' => 'A single line text input',
        ];
    }
}
