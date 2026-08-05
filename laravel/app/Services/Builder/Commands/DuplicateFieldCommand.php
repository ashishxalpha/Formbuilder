<?php
namespace App\Services\Builder\Commands;

class DuplicateFieldCommand implements CommandInterface
{
    protected string $originalFieldId;
    protected string $newFieldId;
    protected string $sectionId;
    protected int $indexInSection;

    public function __construct(string $originalFieldId, string $newFieldId, string $sectionId, array $currentSchema)
    {
        $this->originalFieldId = $originalFieldId;
        $this->newFieldId = $newFieldId;
        $this->sectionId = $sectionId;
        
        if (isset($currentSchema['layout']['sections'])) {
            foreach ($currentSchema['layout']['sections'] as $section) {
                if ($section['id'] === $sectionId) {
                    $this->indexInSection = array_search($originalFieldId, $section['fields']);
                    break;
                }
            }
        }
    }

    public function execute(array $schema): array
    {
        $originalField = null;
        $insertIndex = 0;
        
        foreach ($schema['fields'] as $i => $f) {
            if ($f['id'] === $this->originalFieldId) {
                $originalField = $f;
                $insertIndex = $i + 1;
                break;
            }
        }
        
        if (!$originalField) return $schema;
        
        $newField = $originalField;
        $newField['id'] = $this->newFieldId;
        $newField['key'] = $newField['key'] . '_copy';
        
        array_splice($schema['fields'], $insertIndex, 0, [$newField]);
        
        if (isset($schema['layout']['sections'])) {
            foreach ($schema['layout']['sections'] as &$section) {
                if ($section['id'] === $this->sectionId) {
                    array_splice($section['fields'], $this->indexInSection + 1, 0, [$this->newFieldId]);
                    break;
                }
            }
        }
        return $schema;
    }

    public function undo(array $schema): array
    {
        // Equivalent to DeleteFieldCommand
        $deleteCmd = new DeleteFieldCommand($this->newFieldId, $this->sectionId, $schema);
        return $deleteCmd->execute($schema);
    }
}
