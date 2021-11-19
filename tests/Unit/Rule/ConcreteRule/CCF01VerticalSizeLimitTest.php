<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF01VerticalSizeLimit;
use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class CCF01VerticalSizeLimitTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertSame('CC-F-01 Vertical Size Limit', (new CCF01VerticalSizeLimit())->getName());
    }

    /** @dataProvider complianceProvider */
    public function testCompliance(string $code): void
    {
        $rule = new CCF01VerticalSizeLimit();

        self::assertEquals(
            [Compliance::create($rule)],
            $rule->check($code)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                trim(str_repeat("line\n", 500)),
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(string $code, string $message): void
    {
        $rule = new CCF01VerticalSizeLimit();

        self::assertEquals(
            [Violation::create($rule, $message)],
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