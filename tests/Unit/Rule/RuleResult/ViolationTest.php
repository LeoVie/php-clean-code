<?php

namespace App\Tests\Unit\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class ViolationTest extends TestCase
{
    public function testToString(): void
    {
        $ruleName = 'Rule 1';
        $message = 'The rule was violated.';

        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($ruleName);

        self::assertSame('- Rule 1: ❎ (The rule was violated.)', Violation::create($rule, $message)->toString());
    }
}