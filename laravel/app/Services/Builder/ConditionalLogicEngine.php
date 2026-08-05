<?php

namespace App\Services\Builder;

class ConditionalLogicEngine
{
    /**
     * Evaluates whether a field should be shown/hidden based on current form data and its conditions.
     */
    public function evaluate(array $fieldSchema, array $currentData): array
    {
        $result = [
            'is_visible' => true,
            'is_disabled' => false,
            'is_required' => $fieldSchema['required'] ?? false,
        ];

        if (!isset($fieldSchema['conditions']) || empty($fieldSchema['conditions'])) {
            return $result;
        }

        foreach ($fieldSchema['conditions'] as $conditionGroup) {
            $groupResult = $this->evaluateGroup($conditionGroup, $currentData);
            
            if ($groupResult) {
                // If the condition is met, apply the action
                $action = $conditionGroup['action'] ?? 'show';
                switch ($action) {
                    case 'show':
                        $result['is_visible'] = true;
                        break;
                    case 'hide':
                        $result['is_visible'] = false;
                        break;
                    case 'require':
                        $result['is_required'] = true;
                        break;
                    case 'disable':
                        $result['is_disabled'] = true;
                        break;
                }
            } else {
                // Revert default for "show" logic if group isn't met
                if (($conditionGroup['action'] ?? 'show') === 'show') {
                    $result['is_visible'] = false;
                }
            }
        }

        return $result;
    }

    protected function evaluateGroup(array $group, array $data): bool
    {
        $logicType = strtoupper($group['logic'] ?? 'AND');
        $rules = $group['rules'] ?? [];
        
        if (empty($rules)) return true;

        $results = [];
        foreach ($rules as $rule) {
            $fieldKey = $rule['field'];
            $operator = $rule['operator'];
            $targetValue = $rule['value'];
            $actualValue = $data[$fieldKey] ?? null;

            $results[] = $this->evaluateRule($actualValue, $operator, $targetValue);
        }

        if ($logicType === 'AND') {
            return !in_array(false, $results, true);
        } else {
            return in_array(true, $results, true);
        }
    }

    protected function evaluateRule($actual, string $operator, $target): bool
    {
        return match ($operator) {
            '==' => $actual == $target,
            '===' => $actual === $target,
            '!=' => $actual != $target,
            '>' => $actual > $target,
            '>=' => $actual >= $target,
            '<' => $actual < $target,
            '<=' => $actual <= $target,
            'contains' => is_string($actual) && str_contains($actual, (string)$target),
            'not_contains' => is_string($actual) && !str_contains($actual, (string)$target),
            'in' => is_array($target) && in_array($actual, $target),
            'empty' => empty($actual),
            'not_empty' => !empty($actual),
            default => false,
        };
    }
}
