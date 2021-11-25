<?php

namespace App\Command\CheckDirectory\Output;

use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\RuleResult;
use App\Rule\RuleResult\Violation;
use Symfony\Component\Console\Style\SymfonyStyle;

class HumanOutput implements Output
{
    private const FORMAT = 'human';

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

    public function fileRuleResultsAndScores(array $fileRuleResultsAndScores, bool $showOnlyViolations): self
    {
        foreach ($fileRuleResultsAndScores as $entry) {
            /** @var FileRuleResults $fileRuleResults */
            $fileRuleResults = $entry['file_rule_results'];
            $scores = $entry['scores'];

            if ($showOnlyViolations) {
                if (empty($fileRuleResults->getRuleResultCollection()->getViolations())) {
                    continue;
                }
            }

            [$fileRuleResultsTableHeader, $fileRuleResultsTableRows] = $this->createFileRuleResultsTable($fileRuleResults, $showOnlyViolations);
            [$scoresTableHeader, $scoresTableRows] = $this->createScoresTable($scores);

            $this->symfonyStyle->title($fileRuleResults->getPath());
            $this->symfonyStyle->section('Clean Code Results');
            $this->symfonyStyle->table($fileRuleResultsTableHeader, $fileRuleResultsTableRows);
            $this->symfonyStyle->section('Scores');
            $this->symfonyStyle->table($scoresTableHeader, $scoresTableRows);
        }

        return $this;
    }

    private function createFileRuleResultsTable(FileRuleResults $fileRuleResults, bool $showOnlyViolations): array
    {
        $headers = ['State', 'Rule', 'Message', 'Criticality'];
        $rows = [];

        if ($showOnlyViolations) {
            $ruleResults = $fileRuleResults->getRuleResultCollection()->getViolations();
        } else {
            $ruleResults = $fileRuleResults->getRuleResultCollection()->getRuleResults();
        }

        foreach ($ruleResults as $ruleResult) {
            $rows[] = [
                $this->getStateByRuleResult($ruleResult),
                $ruleResult->getRule()->getName(),
                $ruleResult->getMessage(),
                $ruleResult->getCriticality() . ' %',
            ];
        }

        return [$headers, $rows];
    }

    /** @param Score[] $scores */
    private function createScoresTable(array $scores): array
    {
        $headers = ['Score type', 'Points'];
        $rows = [];
        foreach ($scores as $score) {
            $rows[] = [
                $score->getScoreType(),
                $score->getPoints()
            ];
        }

        return [$headers, $rows];
    }

    private function getStateByRuleResult(RuleResult $ruleResult): string
    {
        return match (true) {
            $ruleResult instanceof Compliance => '<fg=white;bg=green>Compliance</>',
            $ruleResult instanceof Violation => '<error>Violation</error>',
            default => '<comment>WARNING</comment>'
        };
    }
}