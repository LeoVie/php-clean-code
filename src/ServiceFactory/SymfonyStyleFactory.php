<?php

namespace App\ServiceFactory;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyStyleFactory
{
    public function create(InputInterface $symfonyInput, OutputInterface $symfonyOutput): SymfonyStyle
    {
        return new SymfonyStyle($symfonyInput, $symfonyOutput);
    }
}