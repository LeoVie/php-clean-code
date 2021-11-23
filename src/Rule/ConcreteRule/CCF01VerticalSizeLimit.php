<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF01VerticalSizeLimit implements RuleFileCodeAware
{
    private const NAME = 'CC-F-01 Vertical Size Limit';
    private const MAX_VERTICAL_SIZE = 500;
    private const VIOLATION_MESSAGE_PATTERN = 'File has %d lines more than allowed.';
    private const COMPLIANCE_MESSAGE_PATTERN = 'File has %d lines less than allowed maximum.';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(string $code): array
    {
        $lines = explode("\n", $code);
        $linesCount = count($lines);

        if ($linesCount > self::MAX_VERTICAL_SIZE) {
            $message = $this->buildMessage(self::VIOLATION_MESSAGE_PATTERN, $linesCount);

            return [Violation::create($this, $message)];
        }

        $message = $this->buildMessage(self::COMPLIANCE_MESSAGE_PATTERN, $linesCount);
        return [Compliance::create($this, $message)];
    }

    private function buildMessage(string $pattern, int $linesCount): string
    {
        return \Safe\sprintf($pattern, abs($linesCount - self::MAX_VERTICAL_SIZE));
    }
}