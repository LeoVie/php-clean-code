<?php

namespace App\Command\CheckDirectory\Output;

use App\Rule\FileRuleResults;
use Symfony\Component\Console\Output\OutputInterface;

class JsonOutput implements Output
{
    private const FORMAT = 'json';

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
        $this->symfonyOutput->write(\Safe\json_encode(['violations_exist' => false]));

        return $this;
    }

    public function fileRuleResults(array $fileRuleResultsArray): self
    {
        $fileRuleResultsData = array_values(
            array_map(
                fn(FileRuleResults $frr): array => $frr->jsonSerialize(),
                $fileRuleResultsArray
            )
        );

        $this->symfonyOutput->writeln(\Safe\json_encode([
            'violations_exist' => true,
            'violations' => $fileRuleResultsData
        ]));

        return $this;
    }
}