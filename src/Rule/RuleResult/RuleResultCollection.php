<?php

namespace App\Rule\RuleResult;

class RuleResultCollection implements \JsonSerializable
{
    /** @var RuleResult[] */
    private array $ruleResults;

    /** @var Violation[] */
    private array $violations;

    /** @var Compliance[] */
    private array $compliances;

    /** @param RuleResult[] $ruleResults */
    private function __construct(array $ruleResults)
    {
        $this->ruleResults = $this->sortRuleResultsByRuleName($ruleResults);
        $this->violations = $this->extractViolations($ruleResults);
        $this->compliances = $this->extractCompliances($ruleResults);
    }

    /** @param RuleResult[] $ruleResults */
    public static function create(array $ruleResults): self
    {
        return new self($ruleResults);
    }

    /**
     * @param RuleResult[] $ruleResults
     *
     * @return Violation[]
     */
    private function extractViolations(array $ruleResults): array
    {
        return array_values(
            array_filter(
                $ruleResults,
                fn(RuleResult $ruleResult): bool => $ruleResult instanceof Violation
            )
        );
    }

    /**
     * @param RuleResult[] $ruleResults
     *
     * @return Compliance[]
     */
    private function extractCompliances(array $ruleResults): array
    {
        return array_values(
            array_filter(
                $ruleResults,
                fn(RuleResult $ruleResult): bool => $ruleResult instanceof Compliance
            )
        );
    }

    /** @return RuleResult[] */
    public function getRuleResults(): array
    {
        return $this->ruleResults;
    }

    /** @return Violation[] */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /** @return Compliance[] */
    public function getCompliances(): array
    {
        return $this->compliances;
    }

    public function jsonSerialize(): array
    {
        return [
            'violations' => array_map(
                fn(RuleResult $ruleResult): array => $ruleResult->jsonSerialize(),
                $this->sortRuleResultsByRuleName($this->violations)
            ),
            'compliances' => array_map(
                fn(RuleResult $ruleResult): array => $ruleResult->jsonSerialize(),
                $this->sortRuleResultsByRuleName($this->compliances)
            ),
        ];
    }

    /**
     * @param RuleResult[] $ruleResults
     *
     * @return RuleResult[]
     */
    private function sortRuleResultsByRuleName(array $ruleResults): array
    {
        usort($ruleResults, function (RuleResult $a, RuleResult $b): int {
            $aName = $a->getRule()->getName();
            $bName = $b->getRule()->getName();

            if ($aName === $bName) {
                return 0;
            }

            return $aName < $bName ? -1 : 1;
        });

        return $ruleResults;
    }
}