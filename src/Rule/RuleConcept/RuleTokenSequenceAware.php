<?php

namespace App\Rule\RuleConcept;

use App\Rule\RuleResult\RuleResult;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

interface RuleTokenSequenceAware extends Rule
{
    public const AWARE_OF = Rule::TOKEN_SEQUENCE_AWARE;

    /** @return RuleResult[] */
    public function check(TokenSequence $tokenSequence): array;
}