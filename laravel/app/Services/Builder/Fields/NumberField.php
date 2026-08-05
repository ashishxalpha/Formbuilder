<?php
namespace App\Services\Builder\Fields;

class NumberField implements FieldInterface
{
    public static function getType(): string { return 'number'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['numeric'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        if (isset($fieldSchema['validation']['min'])) $rules[] = 'min:' . $fieldSchema['validation']['min'];
        if (isset($fieldSchema['validation']['max'])) $rules[] = 'max:' . $fieldSchema['validation']['max'];
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'number-icon', 'label' => 'Number', 'description' => 'Numeric input']; }
}
