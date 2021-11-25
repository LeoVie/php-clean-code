<?php

namespace App\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;

class Violation implements RuleResult
{
    private function __construct(
        private Rule   $rule,
        private string $message,
        private float    $criticality
    )
    {
    }

    public static function create(Rule $rule, string $message, float $criticality): self
    {
        return new self($rule, $message, $criticality);
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCriticality(): ?float
    {
        return $this->criticality;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'violation',
            'rule' => $this->rule->getName(),
            'message' => $this->message,
            'criticality' => $this->criticality
        ];
    }
}