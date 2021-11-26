<?php

namespace App\Command\CheckDirectory\Output;

use App\Model\ScoresResult;
use Symfony\Component\Console\Style\SymfonyStyle;

interface Output
{
    public function getFormat(): string;

    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): self;

    public function noViolations(): self;

    /** @param ScoresResult[] $scoresResults */
    public function scoresResults(array $scoresResults, bool $showOnlyViolations): self;
}