<?php

namespace App\Services\Builder\Fields;

interface FieldInterface
{
    public static function getType(): string;
    public function getValidationRules(array $fieldSchema): array;
    public function getMetadata(): array;
}
