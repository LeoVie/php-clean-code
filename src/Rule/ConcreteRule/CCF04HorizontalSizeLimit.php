<?php

namespace App\Rule\ConcreteRule;

use App\Calculation\CalculatorConcept\CriticalityCalculator;
use App\Model\Line;
use App\Rule\RuleConcept\RuleLinesAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF04HorizontalSizeLimit implements RuleLinesAware
{
    private const NAME = 'CC-F-04 Horizontal Size Limit';
    private const VIOLATION_MESSAGE_PATTERN = 'Line %d has %d characters more than allowed.';
    private const COMPLIANCE_MESSAGE_PATTERN = 'No too long lines exist in code.';
    private const CRITICALITY_FACTOR = 50;
    private const MAX_HORIZONTAL_SIZE = 120;

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

    private function getMaxHorizontalSize(): int
    {
        return self::MAX_HORIZONTAL_SIZE;
    }

    public function check(array $lines): array
    {
        $tooLongLines = $this->extractTooLongLines($lines);

        if (empty($tooLongLines)) {
            $message = self::COMPLIANCE_MESSAGE_PATTERN;

            return [Compliance::create($this, $message)];
        }

        $violations = [];
        foreach ($tooLongLines as $tooLongLine) {
            $message = $this->buildViolationMessage($tooLongLine);

            $criticality = $this->criticalityCalculator->calculate(
                $tooLongLine->length(),
                $this->getMaxHorizontalSize(),
                $this->getCriticalityFactor()
            );

            $violations[] = Violation::create($this, $message, $criticality);
        }

        return $violations;
    }

    /**
     * @param string[] $lines
     *
     * @return Line[]
     */
    private function extractTooLongLines(array $lines): array
    {
        $tooLongLines = [];
        foreach ($lines as $lineNumber => $lineContent) {
            if ($this->isLineTooLong($lineContent)) {
                $tooLongLines[] = Line::fromLineIndexAndContent($lineNumber, $lineContent);
            }
        }

        return $tooLongLines;
    }

    private function isLineTooLong(string $line): bool
    {
        return strlen($line) > $this->getMaxHorizontalSize();
    }

    private function buildViolationMessage(Line $line): string
    {
        return \Safe\sprintf(
            self::VIOLATION_MESSAGE_PATTERN,
            $line->getLineNumber(),
            $line->length() - $this->getMaxHorizontalSize(),
        );
    }
}