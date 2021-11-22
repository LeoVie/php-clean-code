<?php

namespace App\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;

class Compliance implements RuleResult
{
    private function __construct(private Rule $rule)
    {
    }

    public static function create(Rule $rule): self
    {
        return new self($rule);
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'compliance',
            'rule' => $this->rule->getName(),
        ];
    }

    public function toString(): string
    {
        return \Safe\sprintf('- %s: ✅', $this->rule->getName());
    }
}