<?php

namespace App\Tests\Unit\Find;

use App\Find\PhpFileFinder;
use App\ServiceFactory\FinderFactory;
use PHPUnit\Framework\TestCase;

class PhpFileFinderTest extends TestCase
{
    public function testFindPhpFilesInPath(): void
    {
        $path = \Safe\realpath(__DIR__ . '/../../testdata/Find');

        $expected = [
            $path . '/nested/file_3.php',
            $path . '/file_1.php',
            $path . '/file_2.php',
        ];

        self::assertEqualsCanonicalizing($expected, (new PhpFileFinder(new FinderFactory()))->findPhpFilesInPath($path));
    }
}