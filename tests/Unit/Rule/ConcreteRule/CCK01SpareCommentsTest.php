<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Calculation\AmountCalculator;
use App\Rule\ConcreteRule\CCK01SpareComments;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use App\Tests\TestDouble\Calculation\CriticalityCalculatorDouble;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class CCK01SpareCommentsTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(TokenSequence $tokenSequence, string $message): void
    {
        $rule = new CCK01SpareComments(new CriticalityCalculatorDouble(), new AmountCalculator());

        self::assertEquals(
            [Compliance::create($rule, $message)],
            $rule->check($tokenSequence)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                'tokenSequence' => TokenSequence::create([
                    new \PhpToken(T_OPEN_TAG, ''),
                    new \PhpToken(T_VARIABLE, ''),
                    new \PhpToken(T_WHITESPACE, ''),
                    new \PhpToken(T_LNUMBER, ''),
                ]),
                'message' => 'File has an allowed amount of comment tokens (0.000000, that\'s 5.000000 percent points lower than allowed maximum).',
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(TokenSequence $tokenSequence, string $message): void
    {
        $rule = new CCK01SpareComments(new CriticalityCalculatorDouble(), new AmountCalculator());

        self::assertEquals(
            [Violation::create($rule, $message, 10.0)],
            $rule->check($tokenSequence)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'tokenSequence' => TokenSequence::create([
                    new \PhpToken(T_OPEN_TAG, ''),
                    new \PhpToken(T_VARIABLE, ''),
                    new \PhpToken(T_COMMENT, ''),
                    new \PhpToken(T_LNUMBER, ''),
                ]),
                'message' => 'File has a too high amount of comment tokens (25.000000, that\' s 20.000000 percent points higher than allowed).',
            ],
        ];
    }
}