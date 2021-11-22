<?php

namespace App\Command\CheckDirectory\Output;

use App\Rule\FileRuleResults;
use Symfony\Component\Console\Output\OutputInterface;

class HumanOutput implements Output
{
    private const FORMAT = 'human';

    private OutputInterface $symfonyOutput;

    public function getFormat(): string
    {
        return self::FORMAT;
    }

    public function setSymfonyOutput(OutputInterface $symfonyOutput): self
    {
        $this->symfonyOutput = $symfonyOutput;

        return $this;
    }

    public function noViolations(): self
    {
        $this->symfonyOutput->writeln("No errors.");

        return $this;
    }

    public function fileRuleResults(array $fileRuleResultsArray): self
    {
        $fileRuleResultsOutput = join(
            "\n\n",
            array_map(fn(FileRuleResults $frr): string => $frr->toString(), $fileRuleResultsArray)
        );

        $this->symfonyOutput->writeln($fileRuleResultsOutput);

        return $this;
    }
}