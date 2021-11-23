<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF04HorizontalSizeLimit;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class CCF04HorizontalSizeLimitTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(string $code, string $message): void
    {
        $rule = new CCF04HorizontalSizeLimit();

        self::assertEquals(
            [Compliance::create($rule, $message)],
            $rule->check($code)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                'code' => join("\n",
                    [
                        'this is not too long',
                        'and this neither',
                    ]
                ),
                'message' => 'No too long lines exist in code.',
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(string $code, array $messages): void
    {
        $rule = new CCF04HorizontalSizeLimit();

        $expected = [];
        foreach ($messages as $message) {
            $expected[] = Violation::create($rule, $message);
        }

        self::assertEquals(
            $expected,
            $rule->check($code)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'code' => join("\n",
                    [
                        'line with 130 chars---------------------------------------------------------------------------------------------------------------',
                        'line with 120 chars-----------------------------------------------------------------------------------------------------',
                        "line with 120 chars + tab-----------------------------------------------------------------------------------------------\t",
                        'line with 121 chars------------------------------------------------------------------------------------------------------',
                    ]
                ),
                'messages' => [
                    'Line 1 has 10 characters more than allowed.',
                    'Line 4 has 1 characters more than allowed.',
                ],
            ],
        ];
    }
}