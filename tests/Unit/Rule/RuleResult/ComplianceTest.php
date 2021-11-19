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

        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($ruleName);

        self::assertSame('- Rule 1: ✅', Compliance::create($rule)->toString());
    }
}