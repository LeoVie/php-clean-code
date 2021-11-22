<?php

namespace App\Command\CheckDirectory;

use App\Command\CheckDirectory\Output\OutputHolder;
use App\Rule\FileRuleResults;
use App\Service\CleanCodeCheckerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDirectoryCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    private const ARGUMENT_OUTPUT_FORMAT = 'output_format';
    private const SUPPORTED_OUTPUT_FORMATS = [
        'human',
        'json',
    ];
    protected static $defaultName = 'app:check-directory';

    public function __construct(
        private CleanCodeCheckerService $cleanCodeCheckerService,
        private OutputHolder            $outputHolder
    )
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

        $this->addArgument(
            self::ARGUMENT_OUTPUT_FORMAT,
            InputArgument::OPTIONAL,
            \Safe\sprintf('Format of output (possible: %s)', join(', ', self::SUPPORTED_OUTPUT_FORMATS)),
            'human'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $this->extractDirectoryOption($input);
        $outputFormat = $this->extractOutputFormatOption($input);

        $commandOutput = $this->outputHolder->getOutputByFormatAndSymfonyOutput($outputFormat, $output);

        $fileRuleResultsArray = $this->cleanCodeCheckerService->checkDirectory($directory);

        $fileRuleResultsWithViolationsArray = $this->filterOutComplianceResults($fileRuleResultsArray);

        if (empty($fileRuleResultsWithViolationsArray)) {
            $commandOutput->noViolations();

            return Command::SUCCESS;
        }

        $commandOutput->fileRuleResults($fileRuleResultsWithViolationsArray);

        return Command::FAILURE;
    }

    private function extractDirectoryOption(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument(self::ARGUMENT_DIRECTORY);

        return $directory;
    }

    private function extractOutputFormatOption(InputInterface $input): string
    {
        /** @var string $outputFormat */
        $outputFormat = $input->getArgument(self::ARGUMENT_OUTPUT_FORMAT);

        return $outputFormat;
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