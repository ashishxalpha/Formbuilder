<?php
namespace App\Services\Builder\Commands;

class UpdateFieldCommand implements CommandInterface
{
    protected string $fieldId;
    protected array $oldData;
    protected array $newData;

    public function __construct(string $fieldId, array $newData, array $currentSchema)
    {
        $this->fieldId = $fieldId;
        $this->newData = $newData;
        
        foreach ($currentSchema['fields'] as $f) {
            if ($f['id'] === $fieldId) {
                $this->oldData = $f;
                break;
            }
        }
    }

    public function execute(array $schema): array
    {
        foreach ($schema['fields'] as &$f) {
            if ($f['id'] === $this->fieldId) {
                $f = array_merge($f, $this->newData);
                break;
            }
        }
        return $schema;
    }

    public function undo(array $schema): array
    {
        foreach ($schema['fields'] as &$f) {
            if ($f['id'] === $this->fieldId) {
                $f = $this->oldData;
                break;
            }
        }
        return $schema;
    }
}
