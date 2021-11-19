<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF01VerticalSizeLimit implements RuleFileCodeAware
{
    private const NAME = 'CC-F-01 Vertical Size Limit';
    private const MAX_VERTICAL_SIZE = 500;
    private const VIOLATION_MESSAGE_PATTERN = 'File has %d lines more than allowed.';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(string $code): array
    {
        $lines = explode("\n", $code);

        if (count($lines) <= self::MAX_VERTICAL_SIZE) {
            return [Compliance::create($this)];
        }

        $message = \Safe\sprintf(self::VIOLATION_MESSAGE_PATTERN, count($lines) - self::MAX_VERTICAL_SIZE);

        return [Violation::create($this, $message)];
    }
}