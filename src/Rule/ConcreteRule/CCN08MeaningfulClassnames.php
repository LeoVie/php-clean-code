<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleClassNodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Stmt\Class_;

class CCN08MeaningfulClassnames implements RuleClassNodeAware
{
    private const NAME = 'CC-N-08 Meaningful Classnames';
    private const VIOLATION_PATTERN = 'Classname "%s" matches forbidden pattern "%s".';
    private const ANONYMOUS_CLASS_PATTERN = 'Class is anonymous and therefore not forbidden.';
    private const COMPLIANCE_PATTERN = 'Classname "%s" is not forbidden.';
    private const FORBIDDEN_CLASSNAME_PATTERNS = [
        '@.*Manager$@',
        '@.*Processor$@',
    ];
    private const CRITICALITY_FACTOR = 50;

    public function getName(): string
    {
        return self::NAME;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    public function check(Class_ $class): array
    {
        $name = $class->name;
        if ($name === null) {
            return [Compliance::create($this, \Safe\sprintf(self::ANONYMOUS_CLASS_PATTERN))];
        }

        $forbiddenNamePart = $this->getForbiddenNamePart($name->name);
        if ($forbiddenNamePart !== null) {
            $message = \Safe\sprintf(
                self::VIOLATION_PATTERN,
                $name,
                $forbiddenNamePart
            );
            $criticality = $this->getCriticalityFactor();

            return [Violation::create($this, $message, $criticality)];
        }

        $message = \Safe\sprintf(self::COMPLIANCE_PATTERN, $name);

        return [Compliance::create($this, $message)];
    }

    private function getForbiddenNamePart(string $name): ?string
    {
        foreach (self::FORBIDDEN_CLASSNAME_PATTERNS as $pattern) {
            if (preg_match($pattern, $name)) {
                return $pattern;
            }
        }

        return null;
    }
}