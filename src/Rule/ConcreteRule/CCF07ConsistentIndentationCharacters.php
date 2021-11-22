<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF07ConsistentIndentationCharacters implements RuleFileCodeAware
{
    private const NAME = 'CC-F-07 Consistent Indentation Characters';
    private const ALLOWED_INDENTATION_CHARACTER_SEQUENCE = '    ';
    private const VIOLATION_MESSAGE_PATTERN = 'Line %d uses "%s" (ascii %s) for indentation, but should use "%s" (ascii %s).';
    private const ACTUAL_INDENTATION_PATTERN = '@^\s+@';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(string $code): array
    {
        $allowedIndentationCharacterSequenceAscii = $this->stringToAsciiList(self::ALLOWED_INDENTATION_CHARACTER_SEQUENCE);

        $violations = [];
        $lines = explode("\n", $code);
        foreach ($lines as $i => $line) {
            $line = rtrim($line);
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

            $violations[] = Violation::create($this, $message);
        }

        if (!empty($violations)) {
            return $violations;
        }

        return [Compliance::create($this)];
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