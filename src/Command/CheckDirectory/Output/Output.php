<?php

namespace App\Command\CheckDirectory\Output;

use App\Model\ScoresResult;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\StopwatchEvent;

interface Output
{
    public function getFormat(): string;

    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): self;

    public function noViolations(): self;

    /** @param ScoresResult[] $scoresResults */
    public function scoresResults(array $scoresResults, bool $showOnlyViolations): self;

    public function stopTime(StopwatchEvent $stopwatchEvent): self;

    public function initFilesProgressBar(int $countOfFiles): self;

    public function increaseFilesProgressBar(): self;
}