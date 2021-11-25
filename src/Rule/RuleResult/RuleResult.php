<?php

namespace App\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;

interface RuleResult extends \JsonSerializable
{
    public function getRule(): Rule;

    public function getMessage(): string;

    public function getCriticality(): ?float;

    public function jsonSerialize(): array;
}