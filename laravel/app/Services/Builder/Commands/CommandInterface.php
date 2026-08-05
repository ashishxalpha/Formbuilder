<?php

namespace App\Services\Builder\Commands;

interface CommandInterface
{
    public function execute(array $schema): array;
    public function undo(array $schema): array;
}
