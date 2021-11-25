<?php

namespace App\Tests\Unit\Rule\RuleResult;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\RuleResult;
use App\Rule\RuleResult\RuleResultCollection;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class RuleResultCollectionTest extends TestCase
{
    /** @dataProvider getRuleResultsProvider */
    public function testGetRuleResults(array $expected, array $ruleResults): void
    {
        self::assertSame($expected, RuleResultCollection::create($ruleResults)->getRuleResults());
    }

    public function getRuleResultsProvider(): array
    {
        $ruleResults = [
            '01-Rule' => $this->mockRuleResult('01-Rule'),
            '02-Rule (#1)' => $this->mockRuleResult('02-Rule'),
            '02-Rule (#2)' => $this->mockRuleResult('02-Rule'),
            '03-Rule' => $this->mockRuleResult('03-Rule'),
        ];

        return [
            'empty' => [
                'expected' => [],
                'ruleResults' => [],
            ],
            'sorted' => [
                'expected' => array_values($ruleResults),
                'ruleResults' => array_values($ruleResults),
            ],
            'unsorted' => [
                'expected' => array_values($ruleResults),
                'ruleResults' => [
                    $ruleResults['02-Rule (#1)'],
                    $ruleResults['01-Rule'],
                    $ruleResults['02-Rule (#2)'],
                    $ruleResults['03-Rule'],
                ],
            ],
        ];
    }

    private function mockRuleResult(string $ruleName): RuleResult
    {
        $ruleResult = $this->createMock(RuleResult::class);
        $rule = $this->createMock(Rule::class);
        $rule->method('getName')->willReturn($ruleName);
        $ruleResult->method('getRule')->willReturn($rule);

        return $ruleResult;
    }

    /** @dataProvider getViolationsProvider */
    public function testGetViolations(array $expected, array $ruleResults): void
    {
        self::assertSame($expected, RuleResultCollection::create($ruleResults)->getViolations());
    }

    public function getViolationsProvider(): array
    {
        $violations = [
            $this->mockViolation(),
            $this->mockViolation(),
        ];
        $compliances = [
            $this->mockCompliance(),
            $this->mockCompliance(),
        ];

        return [
            'with violations and compliances' => [
                'expected' => $violations,
                'ruleResults' => array_merge($compliances, $violations),
            ],
            'with only violations' => [
                'expected' => $violations,
                'ruleResults' => $violations,
            ],
            'with only compliances' => [
                'expected' => [],
                'ruleResults' => $compliances,
            ],
            'with nothing' => [
                'expected' => [],
                'ruleResults' => [],
            ],
        ];
    }

    private function mockViolation(): Violation
    {
        return $this->createMock(Violation::class);
    }

    private function mockCompliance(): Compliance
    {
        return $this->createMock(Compliance::class);
    }

    /** @dataProvider getCompliancesProvider */
    public function testGetCompliances(array $expected, array $ruleResults): void
    {
        self::assertSame($expected, RuleResultCollection::create($ruleResults)->getCompliances());
    }

    public function getCompliancesProvider(): array
    {
        $violations = [
            $this->mockViolation(),
            $this->mockViolation(),
        ];
        $compliances = [
            $this->mockCompliance(),
            $this->mockCompliance(),
        ];

        return [
            'with violations and compliances' => [
                'expected' => $compliances,
                'ruleResults' => array_merge($violations, $compliances),
            ],
            'with only compliances' => [
                'expected' => $compliances,
                'ruleResults' => $compliances,
            ],
            'with only violations' => [
                'expected' => [],
                'ruleResults' => $violations,
            ],
            'with nothing' => [
                'expected' => [],
                'ruleResults' => [],
            ],
        ];
    }
}