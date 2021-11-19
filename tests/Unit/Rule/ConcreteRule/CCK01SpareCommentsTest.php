<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCK01SpareComments;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PHPUnit\Framework\TestCase;

class CCK01SpareCommentsTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(TokenSequence $tokenSequence): void
    {
        $rule = new CCK01SpareComments();

        self::assertEquals(
            [Compliance::create($rule)],
            $rule->check($tokenSequence)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                TokenSequence::create([
                    new \PhpToken(T_OPEN_TAG, ''),
                    new \PhpToken(T_VARIABLE, ''),
                    new \PhpToken(T_WHITESPACE, ''),
                    new \PhpToken(T_LNUMBER, ''),
                ]),
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(TokenSequence $tokenSequence, string $message): void
    {
        $rule = new CCK01SpareComments();

        self::assertEquals(
            [Violation::create($rule, $message)],
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
                'message' => 'File has a too high amount of comment tokens (20.000000 percent points higher than allowed).',
            ],
        ];
    }
}