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

        self::assertSame($rule, Violation::create($rule, '', 0.0)->getRule());
    }

    public function testGetMessage(): void
    {
        $message = 'Rule failed';

        self::assertSame($message, Violation::create($this->mockRule(), $message, 0.0)->getMessage());
    }

    public function testGetCriticality(): void
    {
        $criticality = 18.9;

        self::assertSame($criticality, Violation::create($this->mockRule(), '', $criticality)->getCriticality());
    }

    public function testJsonSerialize(): void
    {
        $ruleName = 'Rule123';
        $message = 'Rule failed';
        $criticality = 15.5;

        $expected = [
            'type' => 'violation',
            'rule' => $ruleName,
            'message' => $message,
            'criticality' => $criticality
        ];

        self::assertSame($expected, Violation::create($this->mockRule($ruleName), $message, $criticality)->jsonSerialize());
    }

    private function mockRule(string $name = ''): Rule
    {
        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($name);

        return $rule;
    }
}