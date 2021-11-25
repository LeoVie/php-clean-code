<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF01VerticalSizeLimit;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use App\Tests\TestDouble\Calculation\CriticalityCalculatorDouble;
use PHPUnit\Framework\TestCase;

class CCF01VerticalSizeLimitTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new CCF01VerticalSizeLimit(new CriticalityCalculatorDouble());

        self::assertSame('CC-F-01 Vertical Size Limit', $rule->getName());
    }

    /** @dataProvider complianceProvider */
    public function testCompliance(string $code, string $message): void
    {
        $rule = new CCF01VerticalSizeLimit(new CriticalityCalculatorDouble());

        self::assertEquals(
            [Compliance::create($rule, $message)],
            $rule->check($code)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                'code' => "line",
                'message' => 'File has 1 lines.',
            ],
            [
                'code' => trim(str_repeat("line\n", 500)),
                'message' => 'File has 500 lines.',
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(string $code, string $message): void
    {
        $rule = new CCF01VerticalSizeLimit(new CriticalityCalculatorDouble());

        self::assertEquals(
            [Violation::create($rule, $message, 10.0)],
            $rule->check($code)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'code' => trim(str_repeat("line\n", 501)),
                'message' => 'File has 1 lines more than allowed.',
            ],
        ];
    }
}