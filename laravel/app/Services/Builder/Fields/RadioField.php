<?php
namespace App\Services\Builder\Fields;

class RadioField implements FieldInterface
{
    public static function getType(): string { return 'radio'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = [];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        
        $options = array_map(fn($opt) => $opt['value'] ?? $opt['label'], $fieldSchema['options'] ?? []);
        if (!empty($options)) {
            $rules[] = 'in:' . implode(',', $options);
        }
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'radio-icon', 'label' => 'Radio Buttons', 'description' => 'Single choice options']; }
}
