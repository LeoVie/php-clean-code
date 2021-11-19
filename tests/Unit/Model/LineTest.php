<?php

namespace App\Tests\Unit\Model;

use App\Model\Line;
use PHPUnit\Framework\TestCase;

class LineTest extends TestCase
{
    public function testFromLineIndexAndContent(): void
    {
        $line = Line::fromLineIndexAndContent(4, 'this is the line content');

        self::assertSame(5, $line->getLineNumber());
        self::assertSame('this is the line content', $line->getContent());
    }
}