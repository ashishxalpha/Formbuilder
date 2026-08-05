<?php

namespace App\Services\Builder;

use App\Services\Builder\Commands\CommandInterface;

class BuilderService
{
    protected array $undoStack = [];
    protected array $redoStack = [];

    public function executeCommand(CommandInterface $command, array $currentSchema): array
    {
        $newSchema = $command->execute($currentSchema);
        $this->undoStack[] = $command;
        $this->redoStack = []; // Clear redo stack on new action
        return $newSchema;
    }

    public function undo(array $currentSchema): array
    {
        if (empty($this->undoStack)) {
            return $currentSchema;
        }

        /** @var CommandInterface $command */
        $command = array_pop($this->undoStack);
        $newSchema = $command->undo($currentSchema);
        $this->redoStack[] = $command;
        return $newSchema;
    }

    public function redo(array $currentSchema): array
    {
        if (empty($this->redoStack)) {
            return $currentSchema;
        }

        /** @var CommandInterface $command */
        $command = array_pop($this->redoStack);
        $newSchema = $command->execute($currentSchema);
        $this->undoStack[] = $command;
        return $newSchema;
    }
}
