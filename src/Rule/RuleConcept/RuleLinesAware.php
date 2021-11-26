<?php

namespace App\Rule\RuleConcept;

use App\Rule\RuleResult\RuleResult;

interface RuleLinesAware extends Rule
{
    public const AWARE_OF = Rule::LINES_AWARE;

    /**
     * @param string[] $lines
     *
     * @return RuleResult[]
     */
    public function check(array $lines): array;
}