<?php

namespace App\Command\CheckDirectory\Output;

use App\ServiceFactory\SymfonyStyleFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OutputHolder
{
    /** @param iterable<Output> $outputs */
    public function __construct(private iterable $outputs, private SymfonyStyleFactory $symfonyStyleFactory)
    {}

    public function getOutputByFormatAndSymfonyIO(string $format, InputInterface $symfonyInput, OutputInterface $symfonyOutput): Output
    {
        foreach ($this->outputs as $output) {
            if ($output->getFormat() === $format) {
                return $output->setSymfonyStyle($this->symfonyStyleFactory->create($symfonyInput, $symfonyOutput));
            }
        }

        throw new \Exception(\Safe\sprintf('No output found for format "%s".', $format));
    }
}