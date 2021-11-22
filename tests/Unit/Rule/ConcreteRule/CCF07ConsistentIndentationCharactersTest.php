<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF07ConsistentIndentationCharacters;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class CCF07ConsistentIndentationCharactersTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(string $code): void
    {
        $rule = new CCF07ConsistentIndentationCharacters();

        self::assertEquals(
            [Compliance::create($rule)],
            $rule->check($code)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                '    properly indented',
                "    properly indented with line break"
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(string $code, array $messages): void
    {
        $rule = new CCF07ConsistentIndentationCharacters();

        $violations = array_map(
            fn(string $message): Violation => Violation::create($rule, $message),
            $messages
        );

        self::assertEquals(
            $violations,
            $rule->check($code)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'code' =>
                    "  not enough spaces\n"
                    . "     too many spaces\n"
                    . "not indented\n"
                    . "	tab"
                ,
                'messages' => [
                    'Line 1 uses "  " (ascii 32, 32) for indentation, but should use "    " (ascii 32, 32, 32, 32).',
                    'Line 2 uses "     " (ascii 32, 32, 32, 32, 32) for indentation, but should use "    " (ascii 32, 32, 32, 32).',
                    'Line 4 uses "	" (ascii 9) for indentation, but should use "    " (ascii 32, 32, 32, 32).'
                ],
            ],
        ];
    }
}