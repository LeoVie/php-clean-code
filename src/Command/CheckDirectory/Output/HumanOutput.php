<?php

namespace App\Command\CheckDirectory\Output;

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

    /** @inheritDoc */
    public function fileRuleResults(array $fileRuleResultsArray): self
    {
        $headers = ['State', 'Rule', 'Message', 'Criticality'];

        foreach ($fileRuleResultsArray as $fileRuleResults) {
            $this->symfonyStyle->writeln(\Safe\sprintf('<comment>%s</comment>', $fileRuleResults->getPath()));

            $rows = [];
            foreach ($fileRuleResults->getRuleResultCollection()->getRuleResults() as $ruleResult) {
                $rows[] = [
                    $this->getStateByRuleResult($ruleResult),
                    $ruleResult->getRule()->getName(),
                    $ruleResult->getMessage(),
                    $ruleResult->getCriticality() . ' %'
                ];
            }

            $this->symfonyStyle->table($headers, $rows);
        }

        return $this;
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