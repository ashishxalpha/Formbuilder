<?php
namespace App\Services\Builder\Fields;

class RatingField implements FieldInterface
{
    public static function getType(): string { return 'rating'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['integer', 'min:1'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        $max = $fieldSchema['options']['max'] ?? 5;
        $rules[] = 'max:' . $max;
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'star-icon', 'label' => 'Rating', 'description' => 'Star rating input']; }
}
