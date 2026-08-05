<?php

namespace App\Services\Builder\Commands;

class AddFieldCommand implements CommandInterface
{
    protected array $field;
    protected string $sectionId;

    public function __construct(array $field, string $sectionId)
    {
        $this->field = $field;
        $this->sectionId = $sectionId;
    }

    public function execute(array $schema): array
    {
        // Simple logic for adding to first section if layout->sections exists
        if (!isset($schema['layout']['sections'])) {
            $schema['layout']['sections'] = [['id' => $this->sectionId, 'fields' => []]];
        }
        
        $schema['fields'][] = $this->field;
        
        foreach ($schema['layout']['sections'] as &$section) {
            if ($section['id'] === $this->sectionId) {
                $section['fields'][] = $this->field['id'];
                break;
            }
        }
        
        return $schema;
    }

    public function undo(array $schema): array
    {
        // Remove field from fields array
        $schema['fields'] = array_filter($schema['fields'], fn($f) => $f['id'] !== $this->field['id']);
        $schema['fields'] = array_values($schema['fields']);
        
        // Remove field ID from sections
        foreach ($schema['layout']['sections'] as &$section) {
            if ($section['id'] === $this->sectionId) {
                $section['fields'] = array_filter($section['fields'], fn($fId) => $fId !== $this->field['id']);
                $section['fields'] = array_values($section['fields']);
                break;
            }
        }
        
        return $schema;
    }
}
