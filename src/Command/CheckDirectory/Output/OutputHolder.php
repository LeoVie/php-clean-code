<?php

namespace App\Command\CheckDirectory\Output;

use Symfony\Component\Console\Output\OutputInterface;

class OutputHolder
{
    /** @param iterable<Output> $outputs */
    public function __construct(private iterable $outputs)
    {}

    public function getOutputByFormatAndSymfonyOutput(string $format, OutputInterface $symfonyOutput): ?Output
    {
        foreach ($this->outputs as $output) {
            if ($output->getFormat() === $format) {
                return $output->setSymfonyOutput($symfonyOutput);
            }
        }

        throw new \Exception(\Safe\sprintf('No output found for format "%s".', $format));
    }
}