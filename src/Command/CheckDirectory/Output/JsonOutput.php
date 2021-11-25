<?php

namespace App\Command\CheckDirectory\Output;

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

    /** @inheritDoc */
    public function fileRuleResults(array $fileRuleResultsArray): self
    {
        $fileRuleResultsData = array_values(
            array_map(
                fn(FileRuleResults $frr): array => $frr->jsonSerialize(),
                $fileRuleResultsArray
            )
        );

        $violationsExist = false;
        foreach ($fileRuleResultsArray as $fileRuleResult) {
            if (!empty($fileRuleResult->getRuleResultCollection()->getViolations())) {
                $violationsExist = true;
                break;
            }
        }

        $compliancesExist = false;
        foreach ($fileRuleResultsArray as $fileRuleResult) {
            if (!empty($fileRuleResult->getRuleResultCollection()->getCompliances())) {
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