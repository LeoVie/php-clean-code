<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleConcept\RuleLinesAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF07ConsistentIndentationCharacters implements RuleLinesAware
{
    private const NAME = 'CC-F-07 Consistent Indentation Characters';
    private const ALLOWED_INDENTATION_CHARACTER_SEQUENCE = '    ';
    private const VIOLATION_MESSAGE_PATTERN = 'Line %d uses "%s" (ascii %s) for indentation, but should use "%s" (ascii %s).';
    private const COMPLIANCE_MESSAGE_PATTERN = 'Code is properly indented (all lines use "%s" (ascii %s) for indentation).';
    private const ACTUAL_INDENTATION_PATTERN = '@^\s+@';
    private const CRITICALITY_FACTOR = 5;

    public function getName(): string
    {
        return self::NAME;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    public function check(array $lines): array
    {
        $allowedIndentationCharacterSequenceAscii = $this->stringToAsciiList(self::ALLOWED_INDENTATION_CHARACTER_SEQUENCE);

        $violations = [];
        foreach ($lines as $i => $line) {
            $ltrimmedLine = $this->ltrimIndentationCharacters($line);

            $startsWithWhitespaceCharacter = $this->startsWithWhitespaceCharacter($ltrimmedLine);

            if (!$startsWithWhitespaceCharacter) {
                continue;
            }

            preg_match(self::ACTUAL_INDENTATION_PATTERN, $line, $matches);
            $actualIndentationCharacters = $matches[0];

            $lineNumber = $i + 1;

            $message = \Safe\sprintf(
                self::VIOLATION_MESSAGE_PATTERN,
                $lineNumber,
                $actualIndentationCharacters,
                $this->stringToAsciiList($actualIndentationCharacters),
                self::ALLOWED_INDENTATION_CHARACTER_SEQUENCE,
                $allowedIndentationCharacterSequenceAscii
            );

            $criticality = $this->getCriticalityFactor();
            $violations[] = Violation::create($this, $message, $criticality);
        }

        if (!empty($violations)) {
            return $violations;
        }

        $message = \Safe\sprintf(
            self::COMPLIANCE_MESSAGE_PATTERN,
            self::ALLOWED_INDENTATION_CHARACTER_SEQUENCE,
            $allowedIndentationCharacterSequenceAscii
        );

        return [Compliance::create($this, $message)];
    }

    private function stringToAsciiList(string $string): string
    {
        return join(', ', array_map(fn(string $char): int => ord($char), str_split($string)));
    }

    private function ltrimIndentationCharacters(string $subject): string
    {
        $pattern = sprintf('@^(%s)*@', self::ALLOWED_INDENTATION_CHARACTER_SEQUENCE);

        /** @var string $ltrimmed */
        $ltrimmed = \Safe\preg_replace($pattern, '', $subject);

        return $ltrimmed;
    }

    private function startsWithWhitespaceCharacter(string $subject): bool
    {
        if ($subject === '') {
            return false;
        }

        return \Safe\preg_match('@\S@', $subject[0]) !== 1;
    }
}