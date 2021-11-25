<?php

namespace App\Tests\Unit\Rule;

use App\Rule\FileRuleResults;
use App\Rule\RuleResult\RuleResultCollection;
use PHPUnit\Framework\TestCase;

class FileRuleResultsTest extends TestCase
{
    public function testGetPath(): void
    {
        $path = '/var/file.php';

        self::assertSame($path, FileRuleResults::create($path, RuleResultCollection::create([]))->getPath());
    }

    public function testGetRuleResultCollection(): void
    {
        $ruleResultCollection = RuleResultCollection::create([]);

        self::assertSame($ruleResultCollection, FileRuleResults::create('', $ruleResultCollection)->getRuleResultCollection());
    }
}