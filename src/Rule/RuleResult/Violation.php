<?php

namespace App\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;

class Violation implements RuleResult
{
    private function __construct(
        private Rule   $rule,
        private string $message
    )
    {
    }

    public static function create(Rule $rule, string $message): self
    {
        return new self($rule, $message);
    }

    public function toString(): string
    {
        return \Safe\sprintf("- %s: ❎ (%s)", $this->rule->getName(), $this->message);
    }
}