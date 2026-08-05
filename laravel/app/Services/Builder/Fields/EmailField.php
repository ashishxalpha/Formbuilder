<?php
namespace App\Services\Builder\Fields;

class EmailField implements FieldInterface
{
    public static function getType(): string { return 'email'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['email', 'string'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'email-icon', 'label' => 'Email', 'description' => 'Email address input']; }
}
