<?php
namespace App\Services\Builder\Fields;

class PhoneField implements FieldInterface
{
    public static function getType(): string { return 'phone'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['string', 'regex:/^([0-9\s\-\+\(\)]*)$/'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'phone-icon', 'label' => 'Phone', 'description' => 'Phone number input']; }
}
