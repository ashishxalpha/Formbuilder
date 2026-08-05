<?php
namespace App\Services\Builder\Fields;

class DateField implements FieldInterface
{
    public static function getType(): string { return 'date'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['date'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'date-icon', 'label' => 'Date', 'description' => 'Date picker']; }
}
