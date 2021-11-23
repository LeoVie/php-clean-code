<?php

namespace App\Tests\Unit\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Compliance;
use PHPUnit\Framework\TestCase;

class ComplianceTest extends TestCase
{
    public function testToString(): void
    {
        $ruleName = 'Rule 1';
        $message = 'The rule was not violated.';

        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($ruleName);

        self::assertSame('- Rule 1: ✅ (The rule was not violated.)', Compliance::create($rule, $message)->toString());
    }
}