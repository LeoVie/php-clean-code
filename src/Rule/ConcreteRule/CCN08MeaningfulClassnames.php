<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleClassNodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Stmt\Class_;

class CCN08MeaningfulClassnames implements RuleClassNodeAware
{
    private const NAME = 'CC-N-08 Meaningful Classnames';
    private const VIOLATION_MESSAGE_PATTERN = 'Classname "%s" matches forbidden pattern "%s".';
    private const FORBIDDEN_CLASSNAME_PATTERNS = [
        '@.*Manager$@',
        '@.*Processor$@'
    ];

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(Class_ $class): array
    {
        $name = $class->name;
        if ($name === null) {
            return [Compliance::create($this)];
        }

        $forbiddenNamePart = $this->getForbiddenNamePart($name);
        if ($forbiddenNamePart !== null) {
            $message = \Safe\sprintf(
                self::VIOLATION_MESSAGE_PATTERN,
                $name,
                $forbiddenNamePart
            );

            return [Violation::create($this, $message)];
        }

        return [Compliance::create($this)];
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