<?php

namespace App\Command\CheckDirectory\Output;

use App\Command\CheckDirectory\Output\Helper\Model\Table;
use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\RuleResult;
use App\Rule\RuleResult\Violation;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\StopwatchEvent;

class HumanOutput implements Output
{
    public const FORMAT = 'human';

    private SymfonyStyle $symfonyStyle;

    public function getFormat(): string
    {
        return self::FORMAT;
    }

    public function setSymfonyStyle(SymfonyStyle $symfonyStyle): self
    {
        $this->symfonyStyle = $symfonyStyle;

        return $this;
    }

    public function noViolations(): self
    {
        $this->symfonyStyle->writeln("No errors.");

        return $this;
    }

    /** @inheritDoc */
    public function scoresResults(array $scoresResults, bool $showOnlyViolations): self
    {
        foreach ($scoresResults as $scoresResult) {
            $fileRuleResults = $scoresResult->getFileRuleResults();

            if ($showOnlyViolations) {
                if (empty($fileRuleResults->getRuleResultCollection()->getViolations())) {
                    continue;
                }
            }

            $fileRuleResultsTable = $this->createFileRuleResultsTable($fileRuleResults, $showOnlyViolations);
            $scoresTable = $this->createScoresTable($scoresResult->getScores());

            $this->symfonyStyle->title($fileRuleResults->getPath());
            $this->symfonyStyle->section('Clean Code Results');
            $this->symfonyStyle->table($fileRuleResultsTable->getHeader(), $fileRuleResultsTable->getRows());
            $this->symfonyStyle->section('Scores');
            $this->symfonyStyle->table($scoresTable->getHeader(), $scoresTable->getRows());
        }

        return $this;
    }

    private function createFileRuleResultsTable(FileRuleResults $fileRuleResults, bool $showOnlyViolations): Table
    {
        $ruleResults = $this->extractRuleResultsFromFileRuleResults($fileRuleResults, $showOnlyViolations);

        $table = Table::create(['State', 'Rule', 'Message', 'Criticality']);
        foreach ($ruleResults as $ruleResult) {
            $table->addRow([
                $this->getStateByRuleResult($ruleResult),
                $ruleResult->getRule()->getName(),
                $ruleResult->getMessage(),
                $ruleResult->getCriticality() . ' %',
            ]);
        }

        return $table;
    }

    /** @return RuleResult[] */
    private function extractRuleResultsFromFileRuleResults(
        FileRuleResults $fileRuleResults,
        bool            $onlyViolations
    ): array
    {
        if ($onlyViolations) {
            return $fileRuleResults->getRuleResultCollection()->getViolations();
        }

        return $fileRuleResults->getRuleResultCollection()->getRuleResults();
    }

    /** @param Score[] $scores */
    private function createScoresTable(array $scores): Table
    {
        $table = Table::create(['Score type', 'Points']);
        foreach ($scores as $score) {
            $table->addRow([
                $score->getScoreType(),
                (string)$score->getPoints(),
            ]);
        }

        return $table;
    }

    private function getStateByRuleResult(RuleResult $ruleResult): string
    {
        return match (true) {
            $ruleResult instanceof Compliance => '<fg=white;bg=green>Compliance</>',
            $ruleResult instanceof Violation => '<error>Violation</error>',
            default => '<comment>WARNING</comment>'
        };
    }

    public function stopTime(StopwatchEvent $stopwatchEvent): self
    {
        $this->symfonyStyle->writeln($stopwatchEvent->__toString());

        return $this;
    }

    public function initFilesProgressBar(int $countOfFiles): self
    {
        $this->symfonyStyle->progressStart($countOfFiles);

        return $this;
    }

    public function increaseFilesProgressBar(): self
    {
        $this->symfonyStyle->progressAdvance();

        return $this;
    }
}