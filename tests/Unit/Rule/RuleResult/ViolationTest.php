<?php

namespace App\Tests\Unit\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class ViolationTest extends TestCase
{
    public function testGetRule(): void
    {
        $rule = $this->mockRule();

        self::assertSame($rule, Violation::create($rule, '')->getRule());
    }

    public function testGetMessage(): void
    {
        $message = 'Rule failed';

        self::assertSame($message, Violation::create($this->mockRule(), $message)->getMessage());
    }

    public function testJsonSerialize(): void
    {
        $ruleName = 'Rule123';
        $message = 'Rule failed';

        $expected = [
            'type' => 'violation',
            'rule' => $ruleName,
            'message' => $message,
        ];

        self::assertSame($expected, Violation::create($this->mockRule($ruleName), $message)->jsonSerialize());
    }

    private function mockRule(string $name = ''): Rule
    {
        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($name);

        return $rule;
    }
}