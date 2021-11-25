<?php

namespace App\Rule\ConcreteRule;

use App\Calculation\CalculatorConcept\CriticalityCalculator;
use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF01VerticalSizeLimit implements RuleFileCodeAware
{
    private const NAME = 'CC-F-01 Vertical Size Limit';
    private const VIOLATION_MESSAGE_PATTERN = 'File has %d lines more than allowed.';
    private const COMPLIANCE_MESSAGE_PATTERN = 'File has %d lines.';
    private const CRITICALITY_FACTOR = 50;
    private const MAX_VERTICAL_SIZE = 500;

    public function __construct(private CriticalityCalculator $criticalityCalculator)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    private function getMaxVerticalSize(): int
    {
        return self::MAX_VERTICAL_SIZE;
    }

    public function check(string $code): array
    {
        $lines = explode("\n", $code);
        $linesCount = count($lines);

        if ($linesCount > $this->getMaxVerticalSize()) {
            $message = $this->buildViolationMessage($linesCount);

            $criticality = $this->criticalityCalculator->calculate(
                $linesCount,
                $this->getMaxVerticalSize(),
                $this->getCriticalityFactor()
            );

            return [Violation::create($this, $message, $criticality)];
        }

        $message = $this->buildComplianceMessage($linesCount);

        return [Compliance::create($this, $message)];
    }

    private function buildViolationMessage(int $linesCount): string
    {
        return \Safe\sprintf(self::VIOLATION_MESSAGE_PATTERN, $linesCount - $this->getMaxVerticalSize());
    }

    private function buildComplianceMessage(int $linesCount): string
    {
        return \Safe\sprintf(self::COMPLIANCE_MESSAGE_PATTERN, $linesCount);
    }
}