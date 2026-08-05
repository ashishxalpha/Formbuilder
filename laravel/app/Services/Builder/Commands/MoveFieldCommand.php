<?php
namespace App\Services\Builder\Commands;

class MoveFieldCommand implements CommandInterface
{
    protected string $fieldId;
    protected string $fromSectionId;
    protected string $toSectionId;
    protected int $fromIndex;
    protected int $toIndex;

    public function __construct(string $fieldId, string $fromSectionId, string $toSectionId, int $fromIndex, int $toIndex)
    {
        $this->fieldId = $fieldId;
        $this->fromSectionId = $fromSectionId;
        $this->toSectionId = $toSectionId;
        $this->fromIndex = $fromIndex;
        $this->toIndex = $toIndex;
    }

    public function execute(array $schema): array
    {
        if (!isset($schema['layout']['sections'])) return $schema;

        // Remove from original
        foreach ($schema['layout']['sections'] as &$section) {
            if ($section['id'] === $this->fromSectionId) {
                array_splice($section['fields'], $this->fromIndex, 1);
                break;
            }
        }

        // Add to new
        foreach ($schema['layout']['sections'] as &$section) {
            if ($section['id'] === $this->toSectionId) {
                array_splice($section['fields'], $this->toIndex, 0, [$this->fieldId]);
                break;
            }
        }

        return $schema;
    }

    public function undo(array $schema): array
    {
        // Reverse operation
        $reverseCmd = new MoveFieldCommand($this->fieldId, $this->toSectionId, $this->fromSectionId, $this->toIndex, $this->fromIndex);
        return $reverseCmd->execute($schema);
    }
}
