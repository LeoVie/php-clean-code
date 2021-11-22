<?php

namespace App\Rule\RuleResult;

interface RuleResult extends \JsonSerializable
{
    public function toString(): string;
}