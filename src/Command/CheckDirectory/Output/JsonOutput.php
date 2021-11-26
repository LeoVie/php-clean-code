<?php

namespace App\Command\CheckDirectory\Output;

use App\Model\Score;
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

    /** @inheritDoc */
    public function scoresResults(array $scoresResults, bool $showOnlyViolations): self
    {
        $fileRuleResultsData = [];
        foreach ($scoresResults as $scoresResult) {
            $fileRuleResults = $scoresResult->getFileRuleResults();

            $fileRuleResultsData[] = [
                'file_rule_results' => $fileRuleResults->jsonSerialize(),
                'scores' => array_map(
                    fn(Score $score): array => $score->jsonSerialize(),
                    $scoresResult->getScores()
                ),
            ];
        }

        $violationsExist = false;
        foreach ($scoresResults as $scoresResult) {
            $fileRuleResults = $scoresResult->getFileRuleResults();
            if (!empty($fileRuleResults->getRuleResultCollection()->getViolations())) {
                $violationsExist = true;
                break;
            }
        }

        $compliancesExist = false;
        foreach ($scoresResults as $scoresResult) {
            $fileRuleResults = $scoresResult->getFileRuleResults();
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