<?php
namespace App\Services\Builder\Fields;

class DropdownField implements FieldInterface
{
    public static function getType(): string { return 'dropdown'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = [];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        
        $options = array_map(fn($opt) => $opt['value'] ?? $opt['label'], $fieldSchema['options'] ?? []);
        if (!empty($options)) {
            $rules[] = 'in:' . implode(',', $options);
        }
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'dropdown-icon', 'label' => 'Dropdown', 'description' => 'Select from a list']; }
}
