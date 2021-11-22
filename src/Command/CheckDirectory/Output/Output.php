<?php

namespace App\Command\CheckDirectory\Output;

use Symfony\Component\Console\Output\OutputInterface;

interface Output
{
    public function getFormat(): string;

    public function setSymfonyOutput(OutputInterface $symfonyOutput): self;

    public function noViolations(): self;

    public function fileRuleResults(array $fileRuleResultsArray): self;
}