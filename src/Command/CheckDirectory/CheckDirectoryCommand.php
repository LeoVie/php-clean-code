<?php

namespace App\Command\CheckDirectory;

use App\Command\CheckDirectory\Output\OutputHolder;
use App\Rule\FileRuleResults;
use App\Rule\RuleResult\RuleResultCollection;
use App\Service\CleanCodeCheckerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDirectoryCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    private const ARGUMENT_OUTPUT_FORMAT = 'output_format';
    private const OPTION_SHOW_ONLY_VIOLATIONS_LONG = 'show_only_violations';
    private const OPTION_SHOW_ONLY_VIOLATIONS_SHORT = 'o';
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

        $this->addOption(
            self::OPTION_SHOW_ONLY_VIOLATIONS_LONG,
            self::OPTION_SHOW_ONLY_VIOLATIONS_SHORT,
            InputOption::VALUE_OPTIONAL,
            'Set to true, if only violations should get shown. Otherwise also compliances will be shown.',
            false,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $this->extractDirectoryArgument($input);
        $outputFormat = $this->extractOutputFormatArgument($input);
        $showOnlyViolations = $this->extractShowOnlyViolationsOption($input);

        $commandOutput = $this->outputHolder->getOutputByFormatAndSymfonyOutput($outputFormat, $output);

        $fileRuleResultsArray = $this->cleanCodeCheckerService->checkDirectory($directory);

        $fileRuleResultsWithViolationsArray = $this->filterOutResultsWithNoViolations($fileRuleResultsArray);

        if (empty($fileRuleResultsWithViolationsArray)) {
            $commandOutput->noViolations();

            return Command::SUCCESS;
        }

        if ($showOnlyViolations) {
            $onlyViolationsFileRuleResultsArray = array_map(
                fn(FileRuleResults $frr): FileRuleResults => FileRuleResults::create(
                    $frr->getPath(),
                    RuleResultCollection::create($frr->getRuleResultCollection()->getViolations()),
                ),
                $fileRuleResultsWithViolationsArray
            );

            $commandOutput->fileRuleResults($onlyViolationsFileRuleResultsArray);

            return Command::FAILURE;
        }

        $commandOutput->fileRuleResults($fileRuleResultsArray);

        return Command::FAILURE;
    }

    private function extractDirectoryArgument(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument(self::ARGUMENT_DIRECTORY);

        return $directory;
    }

    private function extractOutputFormatArgument(InputInterface $input): string
    {
        /** @var string $outputFormat */
        $outputFormat = $input->getArgument(self::ARGUMENT_OUTPUT_FORMAT);

        return $outputFormat;
    }

    private function extractShowOnlyViolationsOption(InputInterface $input): bool
    {
        /** @var string $showOnlyViolations */
        $showOnlyViolations = $input->getOption(self::OPTION_SHOW_ONLY_VIOLATIONS_LONG);

        if (boolval($showOnlyViolations) === false || $showOnlyViolations === 'false') {
            return false;
        }

        return true;
    }

    /**
     * @param FileRuleResults[] $fileRuleResultsArray
     *
     * @return FileRuleResults[]
     */
    private function filterOutResultsWithNoViolations(array $fileRuleResultsArray): array
    {
        return array_filter(
            $fileRuleResultsArray,
            fn(FileRuleResults $fileRuleResults): bool => !empty($fileRuleResults->getRuleResultCollection()->getViolations())
        );
    }
}