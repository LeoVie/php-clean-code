<?php

namespace App\Tests\Unit\Rule;

use App\Rule\FileRuleResults;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use PHPUnit\Framework\TestCase;

class FileRuleResultsTest extends TestCase
{
    public function testGetPath(): void
    {
        $path = '/var/file.php';

        self::assertSame($path, FileRuleResults::create($path, [])->getPath());
    }

    public function testGetViolations(): void
    {
        $ruleResults = [
            $this->createMock(Compliance::class),
            $this->createMock(Compliance::class),
            $this->createMock(Violation::class),
            $this->createMock(Compliance::class),
            $this->createMock(Violation::class),
        ];

        $violations = [
            $ruleResults[2],
            $ruleResults[4],
        ];

        self::assertSame($violations, FileRuleResults::create('', $ruleResults)->getViolations());
    }

    public function testToString(): void
    {
        $expected
            = "/var/file.php:"
            . "\n\tRule 1"
            . "\n\tRule 2";

        $ruleResults = [
            $this->mockViolation('Rule 1'),
            $this->mockViolation('Rule 2'),
        ];

        self::assertSame($expected, FileRuleResults::create('/var/file.php', $ruleResults)->toString());
    }

    private function mockViolation(string $asString): Violation
    {
        $violation = $this->createMock(Violation::class);
        $violation->method('toString')->willReturn($asString);

        return $violation;
    }
}