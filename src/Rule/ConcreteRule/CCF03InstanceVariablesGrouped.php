<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleConcept\RuleClassNodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;

class CCF03InstanceVariablesGrouped implements RuleClassNodeAware
{
    private const NAME = 'CC-F-03 Instance Variables Grouped';
    private const VIOLATION_MESSAGE_PATTERN = 'Class "%s" has ungrouped instance variables.';
    private const COMPLIANCE_MESSAGE_PATTERN = 'Class "%s" has no ungrouped instance variables.';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(Class_ $class): array
    {
        $currentlyInInstanceVariableGroup = false;
        $instanceVariableGroupFinished = false;
        foreach ($class->stmts as $stmt) {
            if (!$stmt instanceof Property) {
                if ($currentlyInInstanceVariableGroup) {
                    $instanceVariableGroupFinished = true;
                }
                continue;
            }

            if ($instanceVariableGroupFinished) {
                $message = $this->buildMessage(self::VIOLATION_MESSAGE_PATTERN, $class);

                return [Violation::create($this, $message)];
            }

            $currentlyInInstanceVariableGroup = true;
        }

        $message = $this->buildMessage(self::COMPLIANCE_MESSAGE_PATTERN, $class);

        return [Compliance::create($this, $message)];
    }

    private function buildMessage(string $pattern, Class_ $class): string
    {
        return \Safe\sprintf($pattern, $class->name);
    }
}