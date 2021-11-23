<?php

namespace App\Command\CheckDirectory\Output;

use App\Rule\FileRuleResults;
use Symfony\Component\Console\Output\OutputInterface;

interface Output
{
    public function getFormat(): string;

    public function setSymfonyOutput(OutputInterface $symfonyOutput): self;

    public function noViolations(): self;

    /** @param FileRuleResults[] $fileRuleResultsArray */
    public function fileRuleResults(array $fileRuleResultsArray): self;
}