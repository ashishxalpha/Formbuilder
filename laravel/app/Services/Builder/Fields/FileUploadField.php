<?php
namespace App\Services\Builder\Fields;

class FileUploadField implements FieldInterface
{
    public static function getType(): string { return 'file'; }
    public function getValidationRules(array $fieldSchema): array {
        $rules = ['file'];
        $rules[] = ($fieldSchema['required'] ?? false) ? 'required' : 'nullable';
        if (isset($fieldSchema['validation']['mimes'])) $rules[] = 'mimes:' . implode(',', (array)$fieldSchema['validation']['mimes']);
        if (isset($fieldSchema['validation']['max_size'])) $rules[] = 'max:' . $fieldSchema['validation']['max_size'];
        return $rules;
    }
    public function getMetadata(): array { return ['icon' => 'file-icon', 'label' => 'File Upload', 'description' => 'Upload a file']; }
}
