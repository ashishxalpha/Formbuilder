<?php
namespace App\Services\Builder\Fields;

class SectionHeadingField implements FieldInterface
{
    public static function getType(): string { return 'section_heading'; }
    public function getValidationRules(array $fieldSchema): array {
        // Section headings don't have validation rules as they don't capture input
        return [];
    }
    public function getMetadata(): array { return ['icon' => 'heading-icon', 'label' => 'Section Heading', 'description' => 'A visual separator']; }
}
