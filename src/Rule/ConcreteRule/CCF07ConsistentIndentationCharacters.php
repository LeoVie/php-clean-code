<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleLinesAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF07ConsistentIndentationCharacters implements RuleLinesAware
{
    private const NAME = 'CC-F-07 Consistent Indentation Characters';
    private const ALLOWED_INDENTATION_SEQUENCE = '    ';
    private const VIOLATION_PATTERN = 'Line %d uses "%s" (ascii %s) for indentation, but should use "%s" (ascii %s).';
    private const COMPLIANCE_PATTERN = 'Code is properly indented (all lines use "%s" (ascii %s) for indentation).';
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
        $allowedIndentationCharacterSequenceAscii = $this->stringToAsciiList(self::ALLOWED_INDENTATION_SEQUENCE);

        $violations = [];
        foreach ($lines as $i => $line) {
            if ($this->isBlockCommentLine($line)) {
                continue;
            }

            $ltrimmedLine = $this->ltrimIndentationCharacters($line);
            $startsWithWhitespaceCharacter = $this->startsWithWhitespaceCharacter($ltrimmedLine);

            if (!$startsWithWhitespaceCharacter) {
                continue;
            }

            preg_match(self::ACTUAL_INDENTATION_PATTERN, $line, $matches);
            $actualIndentationCharacters = $matches[0];

            $lineNumber = $i + 1;

            $message = \Safe\sprintf(
                self::VIOLATION_PATTERN,
                $lineNumber,
                $actualIndentationCharacters,
                $this->stringToAsciiList($actualIndentationCharacters),
                self::ALLOWED_INDENTATION_SEQUENCE,
                $allowedIndentationCharacterSequenceAscii
            );

            $criticality = $this->getCriticalityFactor();
            $violations[] = Violation::create($this, $message, $criticality);
        }

        if (!empty($violations)) {
            return $violations;
        }

        $message = \Safe\sprintf(
            self::COMPLIANCE_PATTERN,
            self::ALLOWED_INDENTATION_SEQUENCE,
            $allowedIndentationCharacterSequenceAscii
        );

        return [Compliance::create($this, $message)];
    }

    private function isBlockCommentLine(string $line): bool
    {
        $ltrimmedLine = ltrim($line);

        if ($ltrimmedLine === '') {
            return false;
        }

        return $ltrimmedLine[0] === '*';
    }

    private function stringToAsciiList(string $string): string
    {
        $asciiCharacters = array_map(fn(string $char): int => ord($char), str_split($string));

        $characterCounts = [
            [
                'char' => $asciiCharacters[0],
                'n' => 1,
            ],
        ];
        $i = 0;
        foreach (array_slice($asciiCharacters, 1) as $asciiCharacter) {
            $currentChar = $characterCounts[$i]['char'];

            $characterCounts[$i] = [
                'char' => $currentChar,
                'n' => $characterCounts[$i]['n'] + 1,
            ];

            if ($asciiCharacter !== $currentChar) {
                $i++;
            }
        }

        $list = array_map(
            fn(array $characterCount): string => \Safe\sprintf(
                '%s (%d times)', $characterCount['char'], $characterCount['n']
            ),
            $characterCounts
        );

        return join(', ', $list);
    }

    private function ltrimIndentationCharacters(string $subject): string
    {
        $pattern = sprintf('@^(%s)*@', self::ALLOWED_INDENTATION_SEQUENCE);

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