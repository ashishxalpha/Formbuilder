<?php
namespace App\Services\Builder\Fields;

class TextareaField implements FieldInterface
{
    public static function getType(): string { return 'textarea'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['string'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        if (isset($fieldSchema['validation']['min'])) $rules[] = 'min:' . $fieldSchema['validation']['min'];
        if (isset($fieldSchema['validation']['max'])) $rules[] = 'max:' . $fieldSchema['validation']['max'];
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'textarea-icon', 'label' => 'Long Text', 'description' => 'Multi-line text input']; }
}
