<?php

namespace App\Command\CheckDirectory\Output;

use App\Model\Score;
use App\Rule\FileRuleResults;
use Symfony\Component\Console\Style\SymfonyStyle;

class JsonOutput implements Output
{
    private const FORMAT = 'json';

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
        $this->symfonyStyle->write(\Safe\json_encode(['violations_exist' => false]));

        return $this;
    }

    public function fileRuleResultsAndScores(array $fileRuleResultsAndScores, bool $showOnlyViolations): self
    {
        $fileRuleResultsData = [];
        foreach ($fileRuleResultsAndScores as $entry) {
            /** @var FileRuleResults $fileRuleResults */
            $fileRuleResults = $entry['file_rule_results'];
            $scores = $entry['scores'];

            $fileRuleResultsData[] = [
                'file_rule_results' => $fileRuleResults->jsonSerialize(),
                'scores' => array_map(
                    fn(Score $score): array => $score->jsonSerialize(),
                    $scores
                ),
            ];
        }

        $violationsExist = false;
        foreach ($fileRuleResultsAndScores as $entry) {
            $fileRuleResults = $entry['file_rule_results'];
            if (!empty($fileRuleResults->getRuleResultCollection()->getViolations())) {
                $violationsExist = true;
                break;
            }
        }

        $compliancesExist = false;
        foreach ($fileRuleResultsAndScores as $entry) {
            $fileRuleResults = $entry['file_rule_results'];
            if (!empty($fileRuleResults->getRuleResultCollection()->getCompliances())) {
                $compliancesExist = true;
                break;
            }
        }

        $this->symfonyStyle->writeln(\Safe\json_encode([
            'violations_exist' => $violationsExist,
            'compliances_exist' => $compliancesExist,
            'rule_results' => $fileRuleResultsData,
        ]));

        return $this;
    }
}