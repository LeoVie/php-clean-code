<?php

namespace App\Tests\Unit\Rule\ConcreteRule;

use App\Rule\ConcreteRule\CCF03InstanceVariablesGrouped;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPUnit\Framework\TestCase;

class CCF03InstanceVariablesGroupedTest extends TestCase
{
    /** @dataProvider complianceProvider */
    public function testCompliance(Class_ $class): void
    {
        $rule = new CCF03InstanceVariablesGrouped();

        self::assertEquals(
            [Compliance::create($rule)],
            $rule->check($class)
        );
    }

    public function complianceProvider(): array
    {
        $class = $this->createMock(Class_::class);
        $class->stmts = [
            $this->createMock(ClassMethod::class),
            $this->createMock(Property::class),
            $this->createMock(Property::class),
            $this->createMock(ClassMethod::class),
        ];

        return [
            [
                $class
            ],
        ];
    }

    /** @dataProvider violationProvider */
    public function testViolation(Class_ $class, string $message): void
    {
        $rule = new CCF03InstanceVariablesGrouped();

        self::assertEquals(
            [Violation::create($rule, $message)],
            $rule->check($class)
        );
    }

    public function violationProvider(): array
    {
        $class = $this->createMock(Class_::class);
        $class->name = $this->createMock(Identifier::class);
        $class->name->method('__toString')->willReturn('Foo');
        $class->stmts = [
            $this->createMock(Property::class),
            $this->createMock(ClassMethod::class),
            $this->createMock(Property::class),
            $this->createMock(ClassMethod::class),
        ];

        return [
            [
                'class' => $class,
                'message' => 'Class Foo has ungrouped instance variables.'
            ],
        ];
    }
}