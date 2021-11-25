<?php

namespace App\Command\CheckDirectory\Output;

use Symfony\Component\Console\Style\SymfonyStyle;

interface Output
{
    public function getFormat(): string;

    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): self;

    public function noViolations(): self;

    public function fileRuleResultsAndScores(array $fileRuleResultsAndScores, bool $showOnlyViolations): self;
}