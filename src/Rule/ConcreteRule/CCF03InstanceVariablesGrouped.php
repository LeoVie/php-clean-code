<?php

namespace App\Rule\ConcreteRule;

use App\Calculation\CalculatorConcept\CriticalityCalculator;
use App\Rule\RuleConcept\RuleClassNodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;

class CCF03InstanceVariablesGrouped implements RuleClassNodeAware
{
    private const NAME = 'CC-F-03 Instance Variables Grouped';
    private const VIOLATION_PATTERN = 'Class "%s" has ungrouped instance variables (%d groups).';
    private const COMPLIANCE_PATTERN = 'Class "%s" has no ungrouped instance variables.';
    private const CRITICALITY_FACTOR = 100;
    private const MAX_COUNT_OF_INSTANCE_VARIABLE_GROUPS = 1;

    public function __construct(private CriticalityCalculator $criticalityCalculator)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    private function getMaxCountOfInstanceVariableGroups(): int
    {
        return self::MAX_COUNT_OF_INSTANCE_VARIABLE_GROUPS;
    }

    public function check(Class_ $class): array
    {
        $countOfInstanceVariableGroups = $this->calculateCountOfInstanceVariableGroups($class);

        if ($countOfInstanceVariableGroups > $this->getMaxCountOfInstanceVariableGroups()) {
            $message = $this->buildViolationMessage($class, $countOfInstanceVariableGroups);

            $criticality = $this->criticalityCalculator->calculate(
                $countOfInstanceVariableGroups,
                $this->getMaxCountOfInstanceVariableGroups(),
                $this->getCriticalityFactor()
            );

            return [Violation::create($this, $message, $criticality)];
        }

        $message = $this->buildComplianceMessage($class);

        return [Compliance::create($this, $message)];
    }

    private function calculateCountOfInstanceVariableGroups(Class_ $class): int
    {
        $currentGroupIndex = null;
        $groups = [];

        foreach ($class->stmts as $i => $statement) {
            if (!$statement instanceof Property) {
                $currentGroupIndex = null;
                continue;
            }

            if ($currentGroupIndex === null) {
                $currentGroupIndex = $i;
            }
            $groups[$currentGroupIndex][] = $statement;
        }

        return count($groups);
    }

    private function buildViolationMessage(Class_ $class, int $countOfInstanceVariableGroups): string
    {
        return \Safe\sprintf(self::VIOLATION_PATTERN, $class->name, $countOfInstanceVariableGroups);
    }

    private function buildComplianceMessage(Class_ $class): string
    {
        return \Safe\sprintf(self::COMPLIANCE_PATTERN, $class->name);
    }
}