<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;

class CCF07ConsistentIndentationCharacters
{
    private const NAME = 'CC-F-07 Consistent Indentation Characters';
    private const INDENTATION_CHARACTERS = '    ';
    private const VIOLATION_MESSAGE_PATTERN = 'Line %d uses "%s" for indentation, but should use "%s".';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(string $code): array
    {
        $allowedIndentationPattern = \Safe\sprintf('@^(%s)*[^ ].+$@', self::INDENTATION_CHARACTERS);
        $actualIndentationPattern = '@^(\s+)[^ ].+$@';

        $violations = [];
        $lines = explode("\n", $code);
        foreach ($lines as $i => $line) {
            var_dump($line);

            if (!preg_match($allowedIndentationPattern, $line)) {
                preg_match($actualIndentationPattern, $line, $matches);
                $actualIndentationCharacters = $matches[0];

                $message = \Safe\sprintf(
                    self::VIOLATION_MESSAGE_PATTERN,
                    $i + 1,
                    $actualIndentationCharacters,
                    self::INDENTATION_CHARACTERS
                );
                $violations[] = Violation::create($this, $message);
            }
        }

        if (!empty($violations)) {
            return $violations;
        }

        return [Compliance::create($this)];
    }
}