<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF07ConsistentIndentationCharacters;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class CCF07ConsistentIndentationCharactersTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(array $lines, string $message): void
    {
        $rule = new CCF07ConsistentIndentationCharacters();

        self::assertEquals(
            [Compliance::create($rule, $message)],
            $rule->check($lines)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                'lines' => ['    properly indented'],
                'message' => 'Code is properly indented (all lines use "    " (ascii 32, 32, 32, 32) for indentation).'
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(array $lines, array $messages): void
    {
        $rule = new CCF07ConsistentIndentationCharacters();

        $violations = array_map(
            fn(string $message): Violation => Violation::create($rule, $message, 5.0),
            $messages
        );

        self::assertEquals(
            $violations,
            $rule->check($lines)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'lines' => [
                    '  not enough spaces',
                    '     too many spaces',
                    'not indented',
                    '	tab'
                ],
                'messages' => [
                    'Line 1 uses "  " (ascii 32, 32) for indentation, but should use "    " (ascii 32, 32, 32, 32).',
                    'Line 2 uses "     " (ascii 32, 32, 32, 32, 32) for indentation, but should use "    " (ascii 32, 32, 32, 32).',
                    'Line 4 uses "	" (ascii 9) for indentation, but should use "    " (ascii 32, 32, 32, 32).'
                ],
            ],
        ];
    }
}