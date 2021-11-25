<?php

namespace App\Tests\Unit\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Compliance;
use PHPUnit\Framework\TestCase;

class ComplianceTest extends TestCase
{
    public function testGetRule(): void
    {
        $rule = $this->mockRule();

        self::assertSame($rule, Compliance::create($rule, '')->getRule());
    }

    public function testGetMessage(): void
    {
        $message = 'Rule was successful';

        self::assertSame($message, Compliance::create($this->mockRule(), $message)->getMessage());
    }

    public function testJsonSerialize(): void
    {
        $ruleName = 'Rule123';
        $message = 'Rule was successful';

        $expected = [
            'type' => 'compliance',
            'rule' => $ruleName,
            'message' => $message,
        ];

        self::assertSame($expected, Compliance::create($this->mockRule($ruleName), $message)->jsonSerialize());
    }

    private function mockRule(string $name = ''): Rule
    {
        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($name);

        return $rule;
    }
}