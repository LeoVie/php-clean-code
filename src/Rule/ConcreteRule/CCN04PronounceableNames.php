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
    private const VIOLATION_MESSAGE_PATTERN = 'Name "%s" in line %d seems to be unpronounceable.';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(Identifier|Variable $node): array
    {
        $name = $node->name;
        if ($name instanceof Expr) {
            return [Compliance::create($this)];
        }

        if ($this->stringSeemsUnpronounceable($name)) {
            $message = \Safe\sprintf(
                self::VIOLATION_MESSAGE_PATTERN,
                $name,
                $node->getStartLine(),
            );

            return [Violation::create($this, $message)];
        }

        return [Compliance::create($this)];
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
}