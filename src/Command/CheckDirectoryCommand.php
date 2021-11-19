<?php

namespace App\Command;

use App\Rule\FileRuleResults;
use App\Service\CleanCodeCheckerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDirectoryCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    protected static $defaultName = 'app:check-directory';

    public function __construct(private CleanCodeCheckerService $cleanCodeCheckerService)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_DIRECTORY,
            InputArgument::REQUIRED,
            'Absolute path of directory which should get checked'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $directory */
        $directory = $input->getArgument(self::ARGUMENT_DIRECTORY);

        $fileRuleResultsArray = $this->cleanCodeCheckerService->checkDirectory($directory);

        $fileRuleResultsWithViolationsArray = $this->filterOutComplianceResults($fileRuleResultsArray);

        if (empty($fileRuleResultsWithViolationsArray)) {
            $output->writeln("No errors.");

            return Command::SUCCESS;
        }

        $fileRuleResultsOutput = join(
            "\n\n",
            array_map(fn(FileRuleResults $frr): string => $frr->toString(), $fileRuleResultsWithViolationsArray)
        );

        $output->writeln($fileRuleResultsOutput);

        return Command::FAILURE;
    }

    /**
     * @param FileRuleResults[] $fileRuleResultsArray
     *
     * @return FileRuleResults[]
     */
    private function filterOutComplianceResults(array $fileRuleResultsArray): array
    {
        return array_filter(
            $fileRuleResultsArray,
            fn(FileRuleResults $fileRuleResults): bool => !empty($fileRuleResults->getViolations())
        );
    }
}