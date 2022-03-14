<?php

namespace App\Command\CheckDirectory;

use App\Command\CheckDirectory\Output\HumanOutput;
use App\Command\CheckDirectory\Output\JsonOutput;
use App\Command\CheckDirectory\Output\Output;
use App\Command\CheckDirectory\Output\OutputHolder;
use App\Find\PhpFileFinder;
use LeoVie\PhpCleanCode\Rule\FileRuleResults;
use LeoVie\PhpCleanCode\Service\CleanCodeCheckerService;
use LeoVie\PhpCleanCode\Service\CleanCodeScorerService;
use App\ServiceFactory\StopwatchFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDirectoryCommand extends Command
{
    private const ARGUMENT_DIRECTORY = 'directory';
    private const OPTION_OUTPUT_FORMAT_LONG = 'output_format';
    private const OPTION_OUTPUT_FORMAT_SHORT = 'f';
    private const OPTION_SHOW_ONLY_VIOLATIONS_LONG = 'show_only_violations';
    private const OPTION_SHOW_ONLY_VIOLATIONS_SHORT = 'o';
    protected static $defaultName = 'php-clean-code:check';

    public function __construct(
        private CleanCodeCheckerService $cleanCodeCheckerService,
        private CleanCodeScorerService  $cleanCodeScorerService,
        private OutputHolder            $outputHolder,
        private PhpFileFinder           $phpFileFinder,
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

        $this->addOption(
            self::OPTION_OUTPUT_FORMAT_LONG,
            self::OPTION_OUTPUT_FORMAT_SHORT,
            InputArgument::OPTIONAL,
            \Safe\sprintf('Format of output (possible: %s)', join(', ',
                array_map(
                    fn(Output $o): string => $o->getFormat(),
                    $this->outputHolder->getAll()
                )
            )),
            HumanOutput::FORMAT
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
        $stopwatch = StopwatchFactory::create();
        $stopwatch->start('check-directory');

        $directory = $this->extractDirectoryArgument($input);
        $outputFormat = $this->extractOutputFormatOption($input);
        $showOnlyViolations = $this->extractShowOnlyViolationsOption($input);

        $commandOutput = $this->outputHolder->getOutputByFormatAndSymfonyIO($outputFormat, $input, $output);

        $phpFiles = $this->phpFileFinder->findPhpFilesInPath($directory);

        $fileRuleResultsArray = [];
        foreach ($commandOutput->createProgressIterator($phpFiles) as $phpFile) {
            $fileRuleResultsArray[] = $this->cleanCodeCheckerService->checkFile($phpFile);
        }

        $this->cleanCodeCheckerService->saveCache();

        $fileRuleResultsWithViolationsArray = $this->filterOutResultsWithNoViolations($fileRuleResultsArray);

        if (empty($fileRuleResultsWithViolationsArray)) {
            $commandOutput->noViolations()
                ->stopTime($stopwatch->stop('check-directory'));

            return Command::SUCCESS;
        }

        $scoresResults = [];
        foreach ($fileRuleResultsArray as $fileRuleResults) {
            $scoresResults[] = $this->cleanCodeScorerService->createScoresResult($fileRuleResults);
        }

        $commandOutput->scoresResults($scoresResults, $showOnlyViolations)
            ->stopTime($stopwatch->stop('check-directory'));

        return Command::FAILURE;
    }

    private function extractDirectoryArgument(InputInterface $input): string
    {
        /** @var string $directory */
        $directory = $input->getArgument(self::ARGUMENT_DIRECTORY);

        return $directory;
    }

    private function extractOutputFormatOption(InputInterface $input): string
    {
        /** @var string $outputFormat */
        $outputFormat = $input->getOption(self::OPTION_OUTPUT_FORMAT_LONG);

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
            fn(FileRuleResults $fileRuleResults): bool => !empty(
                $fileRuleResults->getRuleResultCollection()->getViolations()
            )
        );
    }
}