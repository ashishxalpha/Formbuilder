<?php
namespace App\Services\Builder\Fields;

class CheckboxField implements FieldInterface
{
    public static function getType(): string { return 'checkbox'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['array'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        
        // Detailed array validation can be handled here or in compiler
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'checkbox-icon', 'label' => 'Checkboxes', 'description' => 'Multiple choice options']; }
}
