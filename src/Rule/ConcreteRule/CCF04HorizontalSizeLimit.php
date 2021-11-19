<?php

namespace App\Rule\ConcreteRule;

use App\Model\Line;
use App\Rule\RuleConcept\Rule;
use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF04HorizontalSizeLimit implements RuleFileCodeAware
{
    private const NAME = 'CC-F-04 Horizontal Size Limit';
    private const MAX_HORIZONTAL_SIZE = 120;
    private const VIOLATION_MESSAGE_PATTERN = 'Line %d has %d characters more than allowed.';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(string $code): array
    {
        $tooLongLines = $this->extractTooLongLines($code);

        if (empty($tooLongLines)) {
            return [Compliance::create($this)];
        }

        $violations = [];
        foreach ($tooLongLines as $tooLongLine) {
            $violations[] = Violation::create($this, $this->buildViolationMessage($tooLongLine));
        }

        return $violations;
    }

    /** @return Line[] */
    private function extractTooLongLines(string $code): array
    {
        $tooLongLines = [];
        foreach (explode("\n", $code) as $lineNumber => $lineContent) {
            $lineContent = rtrim($lineContent);
            if ($this->isLineTooLong($lineContent)) {
                $tooLongLines[] = Line::fromLineIndexAndContent($lineNumber, $lineContent);
            }
        }

        return $tooLongLines;
    }

    private function isLineTooLong(string $line): bool
    {
        return strlen($line) > self::MAX_HORIZONTAL_SIZE;
    }

    private function buildViolationMessage(Line $line): string
    {
        return \Safe\sprintf(
            self::VIOLATION_MESSAGE_PATTERN,
            $line->getLineNumber(),
            strlen($line->getContent()) - self::MAX_HORIZONTAL_SIZE
        );
    }
}