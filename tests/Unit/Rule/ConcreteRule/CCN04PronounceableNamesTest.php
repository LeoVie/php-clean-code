<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCN04PronounceableNames;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Identifier;
use PHPUnit\Framework\TestCase;

class CCN04PronounceableNamesTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(Identifier $node): void
    {
        $rule = new CCN04PronounceableNames();

        self::assertEquals(
            [Compliance::create($rule)],
            $rule->check($node)
        );
    }

    public function complianceProvider(): array
    {
        return [
            [
                $this->mockIdentifier('pronounceable', 10),
            ],
        ];
    }

    private function mockIdentifier(string $name, int $startLine): Identifier
    {
        $identifier = $this->createMock(Identifier::class);
        $identifier->name = $name;
        $identifier->method('getStartLine')->willReturn($startLine);

        return $identifier;
    }

    /** @dataProvider violationProvider */
    public function testViolation(Identifier $node, string $message): void
    {
        $rule = new CCN04PronounceableNames();

        self::assertEquals(
            [Violation::create($rule, $message)],
            $rule->check($node)
        );
    }

    public function violationProvider(): array
    {
        return [
            [
                'node' => $this->mockIdentifier('prnncbl', 10),
                'message' => 'Name "prnncbl" in line 10 seems to be unpronounceable.',
            ],
        ];
    }
}