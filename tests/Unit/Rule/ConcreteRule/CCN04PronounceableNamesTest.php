<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCN04PronounceableNames;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPUnit\Framework\TestCase;

class CCN04PronounceableNamesTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(Identifier|Variable $node): void
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
            [
                $this->mockIdentifier('aa', 10),
            ],
            [
                $this->mockIdentifier('ee', 10),
            ],
            [
                $this->mockIdentifier('ii', 10),
            ],
            [
                $this->mockIdentifier('oo', 10),
            ],
            [
                $this->mockIdentifier('uu', 10),
            ],
            [
                $this->mockIdentifier('yy', 10),
            ],
            [
                $this->mockVariable(),
            ]
        ];
    }

    private function mockIdentifier(string $name, int $startLine): Identifier
    {
        $identifier = $this->createMock(Identifier::class);
        $identifier->name = $name;
        $identifier->method('getStartLine')->willReturn($startLine);

        return $identifier;
    }

    private function mockVariable(): Variable
    {
        $variable = $this->createMock(Variable::class);
        $variable->name = $this->createMock(Expr::class);

        return $variable;
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
            [
                'node' => $this->mockIdentifier('xx', 10),
                'message' => 'Name "xx" in line 10 seems to be unpronounceable.',
            ],
        ];
    }
}