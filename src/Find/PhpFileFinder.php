<?php

namespace App\Find;

use App\ServiceFactory\FinderFactory;

class PhpFileFinder
{
    public function __construct(
        /** @psalm-readonly */
        private FinderFactory $finderFactory
    )
    {
    }

    /** @return string[] */
    public function findPhpFilesInPath(string $path): array
    {
        return iterator_to_array($this->finderFactory->instance()->in($path)->name('*.php')->files());
    }
}