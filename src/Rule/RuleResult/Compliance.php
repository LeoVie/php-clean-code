<?php

namespace App\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;

class Compliance implements RuleResult
{
    private function __construct(private Rule $rule, private string $message)
    {
    }

    public static function create(Rule $rule, string $message): self
    {
        return new self($rule, $message);
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'compliance',
            'rule' => $this->rule->getName(),
            'message' => $this->message
        ];
    }
}