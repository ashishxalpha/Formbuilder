<?php
namespace App\Services\Builder\Commands;

class DeleteFieldCommand implements CommandInterface
{
    protected array $field;
    protected string $sectionId;
    protected int $indexInSchema;
    protected int $indexInSection;

    public function __construct(string $fieldId, string $sectionId, array $currentSchema)
    {
        $this->sectionId = $sectionId;
        
        // Find field and its index
        foreach ($currentSchema['fields'] as $i => $f) {
            if ($f['id'] === $fieldId) {
                $this->field = $f;
                $this->indexInSchema = $i;
                break;
            }
        }
        
        // Find section index
        if (isset($currentSchema['layout']['sections'])) {
            foreach ($currentSchema['layout']['sections'] as $section) {
                if ($section['id'] === $sectionId) {
                    $this->indexInSection = array_search($fieldId, $section['fields']);
                    break;
                }
            }
        }
    }

    public function execute(array $schema): array
    {
        $fieldId = $this->field['id'];
        
        $schema['fields'] = array_filter($schema['fields'], fn($f) => $f['id'] !== $fieldId);
        $schema['fields'] = array_values($schema['fields']);
        
        if (isset($schema['layout']['sections'])) {
            foreach ($schema['layout']['sections'] as &$section) {
                if ($section['id'] === $this->sectionId) {
                    $section['fields'] = array_filter($section['fields'], fn($fId) => $fId !== $fieldId);
                    $section['fields'] = array_values($section['fields']);
                    break;
                }
            }
        }
        return $schema;
    }

    public function undo(array $schema): array
    {
        array_splice($schema['fields'], $this->indexInSchema, 0, [$this->field]);
        
        if (isset($schema['layout']['sections'])) {
            foreach ($schema['layout']['sections'] as &$section) {
                if ($section['id'] === $this->sectionId) {
                    array_splice($section['fields'], $this->indexInSection, 0, [$this->field['id']]);
                    break;
                }
            }
        }
        return $schema;
    }
}
