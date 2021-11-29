<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleNameNodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;

class CCN04PronounceableNames implements RuleNameNodeAware
{
    private const NAME = 'CC-N-04 Pronounceable Names';
    private const VIOLATION_PATTERN = 'Name "%s" in line %d seems to be unpronounceable.';
    private const COMPLIANCE_PATTERN = 'Name "%s" in line %d seems to be pronounceable.';
    private const NODE_IS_EXPRESSION_PATTERN = 'Name is an expression and therefore pronounceable by definition.';
    private const CRITICALITY_FACTOR = 50;

    public function getName(): string
    {
        return self::NAME;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    public function check(Identifier|Variable $node): array
    {
        $name = $node->name;
        if ($name instanceof Expr) {
            return [Compliance::create($this, self::NODE_IS_EXPRESSION_PATTERN)];
        }

        if ($this->stringSeemsUnpronounceable($name)) {
            $message = $this->buildMessage(self::VIOLATION_PATTERN, $name, $node);
            $criticality = $this->getCriticalityFactor();

            return [Violation::create($this, $message, $criticality)];
        }

        $message = $this->buildMessage(self::COMPLIANCE_PATTERN, $name, $node);
        return [Compliance::create($this, $message)];
    }

    private function stringSeemsUnpronounceable(string $string): bool
    {
        $chars = str_split($string);

        if (count($chars) === 1) {
            return false;
        }

        $vowels = array_filter(
            $chars,
            fn(string $char): bool => $this->isVowelLike($char)
        );

        return empty($vowels);
    }

    private function isVowelLike(string $char): bool
    {
        $vowels = ['A', 'E', 'I', 'O', 'U', 'Y'];

        return in_array(ucfirst($char), $vowels);
    }

    private function buildMessage(string $pattern, string $name, Identifier|Variable $node): string
    {
        return \Safe\sprintf(
            $pattern,
            $name,
            $node->getStartLine(),
        );
    }
}